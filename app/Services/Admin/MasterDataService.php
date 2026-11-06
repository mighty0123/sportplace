<?php

namespace App\Services\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Master data. Nama tabel dan kolom hanya berasal dari konstanta ini
 */
class MasterDataService
{
    private const ICON_DIR = 'master-icons';

    public const TYPES = [
        'olahraga' => [
            'table' => 'master_sports', 'status' => 'status_olahraga', 'name' => 'nama_olahraga', 'entity' => 'MasterSport',
            'label' => 'Cabang olahraga', 'noun' => 'cabang olahraga', 'icon' => true, 'name_max' => 30,
            'usage_sql' => 'SELECT COUNT(*) FROM fields u WHERE u.sport_id = x.id', 'usage_noun' => 'lapangan',
        ],
        'kota' => [
            'table' => 'master_cities', 'status' => 'status_kota', 'name' => 'nama_kota', 'entity' => 'MasterCity',
            'label' => 'Kota', 'noun' => 'kota', 'icon' => false, 'name_max' => 50,
            'usage_sql' => 'SELECT COUNT(*) FROM venues u WHERE u.city_id = x.id', 'usage_noun' => 'venue',
        ],
        'fasilitas' => [
            'table' => 'master_facilities', 'status' => 'status_fasilitas', 'name' => 'nama', 'entity' => 'MasterFacility',
            'label' => 'Fasilitas', 'noun' => 'fasilitas', 'icon' => true, 'name_max' => 100,
            'usage_sql' => 'SELECT COUNT(*) FROM field_facilities u WHERE u.facilities_id = x.id', 'usage_noun' => 'lapangan',
        ],
    ];

    public const CATEGORIES = ['peralatan' => 'Peralatan', 'keamanan' => 'Keamanan', 'umum' => 'Umum'];

    public static function type(string $tipe): array
    {
        return self::TYPES[$tipe] ?? abort(404);
    }

    // read

    public function find(string $tipe, int $id): ?object
    {
        $t = self::type($tipe);

        return DB::table("{$t['table']} as x")->where('x.id', $id)
            ->selectRaw("x.*, ({$t['usage_sql']}) as usage_count")->first();
    }

    /** Alamat gambar untuk ditampilkan */
    public function iconUrl(?string $path): ?string
    {
        $p = trim((string) $path);
        if ($p === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $p)) {
            return $p;
        }
        if (preg_match('#^[a-z][a-z0-9+.\-]*:#i', $p)) {
            return null; // javascript:, data:, dan sejenisnya
        }

        return asset(ltrim($p, '/'));
    }

    // validasi

    public function rules(string $tipe, ?int $id, array $in): array
    {
        $t = self::type($tipe);
        $name = ['required', 'string', "max:{$t['name_max']}"];

        if ($tipe === 'olahraga') {
            $name[] = Rule::unique('master_sports', 'nama_olahraga')->ignore($id);
            $name[] = function ($attr, $value, $fail) use ($id) {
                $slug = Str::slug((string) $value);
                if ($slug === '') {
                    $fail('Nama harus memuat huruf atau angka.');
                } elseif ($id === null && DB::table('master_sports')->where('slug', $slug)->exists()) {
                    // Slug hanya dibuat saat tambah
                    $fail("Nama ini menghasilkan alamat (slug) \"{$slug}\" yang sudah dipakai cabang olahraga lain.");
                }
            };
        } elseif ($tipe === 'kota') {
            $name[] = Rule::unique('master_cities', 'nama_kota')
                ->where(fn ($q) => $q->where('provinsi', $in['provinsi'] ?? ''))->ignore($id);
        } else {
            $name[] = Rule::unique('master_facilities', 'nama')->ignore($id);
        }

        $rules = ['nama' => $name];
        if ($tipe === 'kota') {
            $rules['provinsi'] = ['required', 'string', 'max:50'];
        }
        if ($tipe === 'fasilitas') {
            $rules['formKategori'] = ['required', Rule::in(array_keys(self::CATEGORIES))];
        }
        if ($t['icon']) {
            $rules['icon'] = $this->iconRules($id !== null);
        }

        return $rules;
    }

    public function iconRules(bool $optional): array
    {
        return [
            $optional ? 'nullable' : 'required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:512',
            'dimensions:min_width=32,min_height=32,max_width=1024,max_height=1024',
        ];
    }

    public function messages(string $tipe): array
    {
        $t = self::type($tipe);
        $nm = ucfirst($t['noun']);

        return [
            'nama.required'          => "Nama {$t['noun']} wajib diisi.",
            'nama.max'               => "Nama {$t['noun']} maksimal {$t['name_max']} karakter.",
            'nama.unique'            => $tipe === 'kota' ? 'Kota dengan provinsi yang sama sudah ada.' : "{$nm} dengan nama ini sudah ada.",
            'provinsi.required'      => 'Provinsi wajib diisi.',
            'provinsi.max'           => 'Provinsi maksimal 50 karakter.',
            'formKategori.required'  => 'Kategori wajib dipilih.',
            'formKategori.in'        => 'Kategori tidak dikenal.',
            'icon.required'          => 'Ikon wajib diunggah.',
            'icon.image'             => 'Berkas harus berupa gambar.',
            'icon.mimes'             => 'Format ikon harus PNG, JPG, atau WebP.',
            'icon.max'               => 'Ukuran ikon maksimal 512 KB.',
            'icon.dimensions'        => 'Ukuran ikon harus 32 sampai 1024 piksel.',
        ];
    }

    // tulis

    /** @param array{nama:string, provinsi?:string, kategori?:string} $in sudah divalidasi. @return string pesan untuk pengguna. */
    public function save(string $tipe, ?int $id, array $in, ?UploadedFile $icon, User $admin): string
    {
        $t = self::type($tipe);
        $newIcon = ($t['icon'] && $icon) ? $this->storeIcon($icon) : null;
        $oldIcon = null;

        try {
            $message = DB::transaction(function () use ($tipe, $t, $id, $in, $newIcon, $admin, &$oldIcon) {
                $values = match ($tipe) {
                    'olahraga'  => ['nama_olahraga' => $in['nama']],
                    'kota'      => ['nama_kota' => $in['nama'], 'provinsi' => $in['provinsi']],
                    'fasilitas' => ['nama' => $in['nama'], 'kategori' => $in['kategori']],
                };
                if ($newIcon) {
                    $values['icon_url'] = $newIcon;
                }

                if ($id === null) {
                    if ($tipe === 'olahraga') {
                        $values['slug'] = Str::slug($in['nama']);
                    }
                    $values[$t['status']] = 'aktif';
                    $newId = DB::table($t['table'])->insertGetId($values);
                    $this->audit($tipe, $newId, $admin, 'tambah', null, 'aktif', "{$t['label']} {$in['nama']} ditambahkan.");

                    return "{$t['label']} {$in['nama']} ditambahkan.";
                }

                $before = DB::table($t['table'])->where('id', $id)->lockForUpdate()->first();
                abort_if(! $before, 404);

                $changes = [];
                foreach ($values as $col => $val) {
                    if ((string) ($before->{$col} ?? '') !== (string) $val) {
                        $changes[$col] = [$before->{$col} ?? null, $val];
                    }
                }
                if ($changes === []) {
                    return 'Tidak ada perubahan.';
                }

                DB::table($t['table'])->where('id', $id)->update(array_map(fn ($c) => $c[1], $changes));
                if (isset($changes['icon_url'])) {
                    $oldIcon = $changes['icon_url'][0];
                }
                $this->audit($tipe, $id, $admin, 'ubah', null, null, $this->describe($tipe, $changes));

                return "{$t['label']} {$in['nama']} diperbarui.";
            });
        } catch (Throwable $e) {
            $this->deleteManagedIcon($newIcon);
            throw $e;
        }

        $this->deleteManagedIcon($oldIcon);

        return $message;
    }

    public function setStatus(string $tipe, int $id, string $status, User $admin): string
    {
        abort_unless(in_array($status, ['aktif', 'nonaktif'], true), 422);
        $t = self::type($tipe);
        $old = $status === 'aktif' ? 'nonaktif' : 'aktif';

        return DB::transaction(function () use ($tipe, $t, $id, $status, $old, $admin) {
            $row = DB::table($t['table'])->where('id', $id)->lockForUpdate()->first();
            abort_if(! $row, 404);
            $name = $row->{$t['name']};

            if ($row->{$t['status']} === $status) {
                return "{$t['label']} {$name} sudah {$status}.";
            }

            DB::table($t['table'])->where('id', $id)->update([$t['status'] => $status]);
            $this->audit($tipe, $id, $admin, $status === 'aktif' ? 'aktifkan' : 'nonaktifkan', $old, $status,
                "{$t['label']} {$name} " . ($status === 'aktif' ? 'diaktifkan kembali.' : 'dinonaktifkan.'));

            return "{$t['label']} {$name} " . ($status === 'aktif' ? 'diaktifkan kembali.' : 'dinonaktifkan.');
        });
    }

    // ikon

    private function disk(): string
    {
        return config('admin.master_icon_disk', 'public');
    }

    /** Menyimpan ikon dengan nama acak dan mengembalikan alamat path-nya */
    private function storeIcon(UploadedFile $file): string
    {
        $path = $file->storePublicly(self::ICON_DIR, $this->disk());

        return (string) parse_url(Storage::disk($this->disk())->url($path), PHP_URL_PATH);
    }

    /** Hanya menghapus ikon yang diunggah lewat halaman ini */
    private function deleteManagedIcon(?string $iconUrl): void
    {
        if (! $iconUrl) {
            return;
        }
        $prefix = (string) parse_url(Storage::disk($this->disk())->url(self::ICON_DIR . '/'), PHP_URL_PATH);
        if ($prefix === '' || ! str_starts_with($iconUrl, $prefix)) {
            return;
        }
        $file = substr($iconUrl, strlen($prefix));
        if (preg_match('/^[A-Za-z0-9._-]+$/', $file) && $file !== '.' && $file !== '..') {
            Storage::disk($this->disk())->delete(self::ICON_DIR . '/' . $file);
        }
    }

    // ---- helpers

    private function describe(string $tipe, array $changes): string
    {
        $labels = ['nama_olahraga' => 'nama', 'nama_kota' => 'nama', 'nama' => 'nama', 'provinsi' => 'provinsi', 'kategori' => 'kategori'];
        $parts = [];
        foreach ($changes as $col => [$from, $to]) {
            $parts[] = $col === 'icon_url' ? 'ikon diganti' : ($labels[$col] ?? $col) . ": \"{$from}\" menjadi \"{$to}\"";
        }

        return 'Mengubah ' . implode('; ', $parts) . '.';
    }

    private function audit(string $tipe, int $id, User $admin, string $aksi, ?string $old, ?string $new, string $note): void
    {
        AuditLog::record($admin, self::type($tipe)['entity'], $id, $aksi, $old, $new, Str::limit($note, 500));
    }
}