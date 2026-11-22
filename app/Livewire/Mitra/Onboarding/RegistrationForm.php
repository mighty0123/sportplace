<?php

namespace App\Livewire\Mitra\Onboarding;

use App\Http\Requests\Mitra\MitraOnboardingRequest;
use App\Livewire\Mitra\MitraComponent;
use App\Models\AuditLog;
use App\Models\MitraProfile;
use Illuminate\Contracts\View\View;
use Livewire\WithFileUploads;

class RegistrationForm extends MitraComponent
{
    use WithFileUploads;

    public string $nama_usaha = '';

    public string $bentuk_badan_usaha = 'Perorangan';

    public string $kota_operasional = '';

    public string $deskripsi_usaha = '';

    public string $no_ktp = '';

    public string $npwp = '';

    public string $bank_nama = 'BCA';

    public string $nomor_rekening = '';

    public string $nama_rekening = '';

    public bool $setuju_syarat = false;

    public $file_ktp = null;

    public $file_npwp = null;

    public $file_nib = null;

    public $file_sewa_lahan = null;

    public function mount(): void
    {
        $user = $this->mitraUser();
        $profile = $user->mitraProfile;

        if ($profile) {
            if ($profile->isApproved()) {
                $this->redirectRoute('mitra.dashboard');

                return;
            }

            $this->redirectRoute('mitra.status');
        }
    }

    /**
     * Aturan validasi pendaftaran mitra.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'nama_usaha' => ['required', 'string', 'max:150'],
            'bentuk_badan_usaha' => ['required', 'string', 'max:50'],
            'kota_operasional' => ['required', 'string', 'max:100'],
            'deskripsi_usaha' => ['nullable', 'string', 'max:1000'],
            'no_ktp' => ['required', 'string', 'min:16', 'max:20', 'regex:/^[0-9]+$/'],
            'npwp' => ['nullable', 'string', 'max:24'],
            'bank_nama' => ['required', 'string', 'max:50'],
            'nomor_rekening' => ['required', 'string', 'max:50'],
            'nama_rekening' => ['required', 'string', 'max:100'],
            'file_ktp' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file_npwp' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file_nib' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file_sewa_lahan' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'setuju_syarat' => ['accepted'],
        ];
    }

    /**
     * Pesan error validasi Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        $base = (new MitraOnboardingRequest())->messages();

        return array_merge($base, [
            'no_ktp.regex' => 'Nomor KTP (NIK) hanya boleh berisi angka.',
        ]);
    }

    /**
     * Live validation setiap kali field berubah.
     */
    public function updated(string $propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Hapus file yang dipilih sebelum submit.
     */
    public function removeFile(string $field): void
    {
        if (in_array($field, ['file_ktp', 'file_npwp', 'file_nib', 'file_sewa_lahan'], true)) {
            $this->{$field} = null;
            $this->resetValidation($field);
        }
    }

    /**
     * Simpan pengajuan pendaftaran mitra baru ke disk privat.
     */
    public function submit()
    {
        $user = $this->mitraUser();
        $validated = $this->validate();

        $storagePath = "mitra_documents/{$user->id}";

        $data = [
            'nama_usaha' => $validated['nama_usaha'],
            'bentuk_badan_usaha' => $validated['bentuk_badan_usaha'],
            'kota_operasional' => $validated['kota_operasional'],
            'deskripsi_usaha' => $validated['deskripsi_usaha'],
            'no_ktp' => $validated['no_ktp'],
            'npwp' => $validated['npwp'],
            'bank_nama' => $validated['bank_nama'],
            'nomor_rekening' => $validated['nomor_rekening'],
            'nama_rekening' => $validated['nama_rekening'],
            'file_ktp' => $this->file_ktp->store($storagePath, 'local'),
        ];

        if ($this->file_npwp) {
            $data['file_npwp'] = $this->file_npwp->store($storagePath, 'local');
        }

        if ($this->file_nib) {
            $data['file_nib'] = $this->file_nib->store($storagePath, 'local');
        }

        if ($this->file_sewa_lahan) {
            $data['file_sewa_lahan'] = $this->file_sewa_lahan->store($storagePath, 'local');
        }

        $profile = MitraProfile::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($data, [
                'status' => 'pending',
                'alasan_penolakan' => null,
                'submitted_at' => now(),
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_checklist' => null,
            ])
        );

        AuditLog::record(
            $user,
            'MitraProfile',
            $profile->id,
            'pendaftaran',
            null,
            'pending',
            'Mitra mengirimkan formulir dan berkas legalitas awal.'
        );

        session()->flash('success', 'Pendaftaran mitra berhasil dikirimkan! Pengajuan Anda sedang dalam proses verifikasi.');

        return $this->redirectRoute('mitra.status');
    }

    public function render(): View
    {
        return view('livewire.mitra.onboarding.registration-form', [
            'user' => $this->mitraUser(),
        ]);
    }
}
