<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MitraProfile extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'nama_usaha',
        'bentuk_badan_usaha',
        'kota_operasional',
        'deskripsi_usaha',
        'bank_nama',
        'nomor_rekening',
        'nama_rekening',
        'no_ktp',
        'npwp',
        'status',
        'alasan_penolakan',
        'file_ktp',
        'file_npwp',
        'file_nib',
        'file_sewa_lahan',
        'submitted_at',
    ];

    public const DOCUMENTS = [
        'ktp'  => 'file_ktp',
        'npwp' => 'file_npwp',
        'nib'  => 'file_nib',
        'sewa' => 'file_sewa_lahan',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at'     => 'datetime',
            'reviewed_at'      => 'datetime',
            'review_checklist' => 'array',
        ];
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function hasBusinessDocuments(): bool
    {
        return collect(['npwp', 'nib', 'sewa'])->contains(fn ($type) => $this->documentPath($type));
    }

    /** Data sensitif */
    public const SENSITIVE = ['nik' => 'no_ktp', 'npwp' => 'npwp', 'rekening' => 'nomor_rekening'];

    /** Tersamarkan: NIK menyisakan 4 digit awal dan akhir, lainnya hanya 4 karakter akhir. */
    public function maskedValue(string $field): string
    {
        $value = (string) $this->{self::SENSITIVE[$field]};
        $len   = mb_strlen($value);

        if ($len === 0) {
            return '';
        }
        if ($len <= 8) {
            return str_repeat('•', $len);
        }

        $head = $field === 'nik' ? 4 : 0;

        return Str::mask($value, '•', $head, $len - $head - 4);
    }

    /** Nomor rekening tersamar */
    public function maskedAccountNumber(): string
    {
        $no = (string) $this->nomor_rekening;

        return strlen($no) > 4 ? Str::mask($no, '•', 0, -4) : str_repeat('•', strlen($no));
    }

    // Path berkas di disk privat, atau null kalau jenisnya tidak dikenal / belum diunggah
    public function documentPath(string $type): ?string
    {
        $column = self::DOCUMENTS[$type] ?? null;

        return $column ? ($this->{$column} ?: null) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function venues()
    {
        return $this->hasMany(Venue::class, 'mitra_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function uploadedDocumentsCount(): int
    {
        return collect(self::DOCUMENTS)->filter(fn ($column) => ! empty($this->{$column}))->count();
    }

    public function daysWaiting(): ?int
    {
        return $this->submitted_at ? (int) floor($this->submitted_at->diffInDays(now())) : null;
    }
}
