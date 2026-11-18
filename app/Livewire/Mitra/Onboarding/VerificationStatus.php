<?php

namespace App\Livewire\Mitra\Onboarding;

use App\Http\Requests\Mitra\MitraOnboardingRequest;
use App\Livewire\Mitra\MitraComponent;
use App\Models\AuditLog;
use App\Models\MitraProfile;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

class VerificationStatus extends MitraComponent
{
    use WithFileUploads;

    public bool $showResubmitModal = false;

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

    public function mount(bool $openResubmit = false): void
    {
        $profile = $this->profile();

        if (! $profile) {
            $this->redirectRoute('mitra.onboarding.create');

            return;
        }

        if ($openResubmit && $profile->isRejected()) {
            $this->openResubmitModal();
        }
    }

    #[Computed]
    public function profile(): ?MitraProfile
    {
        return $this->mitraUser()->mitraProfile()->first();
    }

    /**
     * Muat ulang data status verifikasi tanpa reload halaman.
     */
    public function refreshStatus(bool $notifyUser = true): void
    {
        unset($this->profile);

        if ($notifyUser) {
            $this->notify('Status verifikasi berhasil diperbarui.', 'info');
        }
    }

    /**
     * Buka modal perbaikan & ajukan ulang sambil memuat data sebelumnya.
     */
    public function openResubmitModal(): void
    {
        $profile = $this->profile();

        if (! $profile || ! $profile->isRejected()) {
            return;
        }

        $this->nama_usaha = (string) ($profile->nama_usaha ?? '');
        $this->bentuk_badan_usaha = (string) ($profile->bentuk_badan_usaha ?? 'Perorangan');
        $this->kota_operasional = (string) ($profile->kota_operasional ?? '');
        $this->deskripsi_usaha = (string) ($profile->deskripsi_usaha ?? '');
        $this->no_ktp = (string) ($profile->no_ktp ?? '');
        $this->npwp = (string) ($profile->npwp ?? '');
        $this->bank_nama = (string) ($profile->bank_nama ?? 'BCA');
        $this->nomor_rekening = (string) ($profile->nomor_rekening ?? '');
        $this->nama_rekening = (string) ($profile->nama_rekening ?? '');
        $this->setuju_syarat = false;

        $this->file_ktp = null;
        $this->file_npwp = null;
        $this->file_nib = null;
        $this->file_sewa_lahan = null;

        $this->resetValidation();
        $this->showResubmitModal = true;
    }

    public function closeResubmitModal(): void
    {
        $this->showResubmitModal = false;
        $this->resetValidation();
    }

    /**
     * Hapus file yang dipilih di modal sebelum simpan.
     */
    public function removeFile(string $field): void
    {
        if (in_array($field, ['file_ktp', 'file_npwp', 'file_nib', 'file_sewa_lahan'], true)) {
            $this->{$field} = null;
            $this->resetValidation($field);
        }
    }

    /**
     * Aturan validasi untuk ajukan ulang.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $hasExistingKtp = (bool) $this->profile()?->file_ktp;

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
            'file_ktp' => [$hasExistingKtp ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file_npwp' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file_nib' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'file_sewa_lahan' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'setuju_syarat' => ['accepted'],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
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
        if ($this->showResubmitModal) {
            $this->validateOnly($propertyName);
        }
    }

    /**
     * Simpan perbaikan berkas dan ubah status menjadi pending.
     */
    public function resubmit(): void
    {
        $user = $this->mitraUser();
        $profile = $this->profile();

        if (! $profile || ! $profile->isRejected()) {
            $this->closeResubmitModal();

            return;
        }

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
        ];

        if ($this->file_ktp) {
            $data['file_ktp'] = $this->file_ktp->store($storagePath, 'local');
        }

        if ($this->file_npwp) {
            $data['file_npwp'] = $this->file_npwp->store($storagePath, 'local');
        }

        if ($this->file_nib) {
            $data['file_nib'] = $this->file_nib->store($storagePath, 'local');
        }

        if ($this->file_sewa_lahan) {
            $data['file_sewa_lahan'] = $this->file_sewa_lahan->store($storagePath, 'local');
        }

        $alasanLama = $profile->alasan_penolakan;

        $profile->update(array_merge($data, [
            'status' => 'pending',
            'alasan_penolakan' => null,
            'submitted_at' => now(),
            'reviewed_by' => null,
            'reviewed_at' => null,
            'review_checklist' => null,
        ]));

        AuditLog::record(
            $user,
            'MitraProfile',
            $profile->id,
            'ajukan_ulang',
            'rejected',
            'pending',
            "Mitra mengajukan perbaikan berkas setelah penolakan: '{$alasanLama}'"
        );

        $this->showResubmitModal = false;
        unset($this->profile);

        $this->notify('Perbaikan berkas berhasil dikirimkan ulang! Pengajuan Anda sedang ditinjau kembali.', 'success');
    }

    public function render(): View
    {
        return view('livewire.mitra.onboarding.verification-status', [
            'user' => $this->mitraUser(),
            'currentProfile' => $this->profile(),
        ]);
    }
}
