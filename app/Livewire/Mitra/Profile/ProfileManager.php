<?php

namespace App\Livewire\Mitra\Profile;

use App\Livewire\Mitra\MitraComponent;
use App\Models\AuditLog;
use App\Models\MitraProfile;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;

class ProfileManager extends MitraComponent
{
    public bool $showEditModal = false;

    public string $nama_usaha = '';

    public string $bentuk_badan_usaha = 'Perorangan';

    public string $kota_operasional = '';

    public string $deskripsi_usaha = '';

    public string $bank_nama = 'BCA';

    public string $nomor_rekening = '';

    public string $nama_rekening = '';

    public string $user_name = '';

    public string $user_phone = '';

    public string $user_email = '';

    public string $no_ktp = '';

    public string $npwp = '';

    public function mount(): void
    {
        $profile = $this->authorizeMitraApproved();
        $user = $this->mitraUser();

        $this->nama_usaha = (string) ($profile->nama_usaha ?? '');
        $this->bentuk_badan_usaha = (string) ($profile->bentuk_badan_usaha ?? 'Perorangan');
        $this->kota_operasional = (string) ($profile->kota_operasional ?? '');
        $this->deskripsi_usaha = (string) ($profile->deskripsi_usaha ?? '');
        $this->bank_nama = (string) ($profile->bank_nama ?? 'BCA');
        $this->nomor_rekening = (string) ($profile->nomor_rekening ?? '');
        $this->nama_rekening = (string) ($profile->nama_rekening ?? '');

        $this->user_name = (string) ($user->name ?? '');
        $this->user_phone = (string) ($user->phone ?? '');
        $this->user_email = (string) ($user->email ?? '');
        $this->no_ktp = (string) ($profile->no_ktp ?? '');
        $this->npwp = (string) ($profile->npwp ?? '');
    }

    #[Computed]
    public function profile(): MitraProfile
    {
        return $this->authorizeMitraApproved();
    }

    #[Computed]
    public function user(): User
    {
        return $this->mitraUser();
    }

    #[Computed]
    public function documentsList(): array
    {
        $profile = $this->profile();

        return [
            [
                'type' => 'ktp',
                'name' => 'KTP Pemilik Usaha',
                'description' => 'Identitas resmi penanggung jawab mitra.',
                'path' => $profile->file_ktp,
                'status' => ! empty($profile->file_ktp) ? 'Terverifikasi' : 'Belum Ada',
                'badge_color' => ! empty($profile->file_ktp) ? 'emerald' : 'slate',
                'download_url' => ! empty($profile->file_ktp) ? route('mitra.document.download', 'ktp') : null,
            ],
            [
                'type' => 'npwp',
                'name' => 'NPWP Usaha / Pribadi',
                'description' => 'Nomor Pokok Wajib Pajak untuk kepatuhan perpajakan.',
                'path' => $profile->file_npwp,
                'status' => ! empty($profile->file_npwp) ? 'Terverifikasi' : 'Tidak Dilampirkan',
                'badge_color' => ! empty($profile->file_npwp) ? 'emerald' : 'slate',
                'download_url' => ! empty($profile->file_npwp) ? route('mitra.document.download', 'npwp') : null,
            ],
            [
                'type' => 'nib',
                'name' => 'NIB (Nomor Induk Berusaha)',
                'description' => 'Perizinan legalitas operasional berbasis OSS.',
                'path' => $profile->file_nib,
                'status' => ! empty($profile->file_nib) ? 'Terverifikasi' : 'Tidak Dilampirkan',
                'badge_color' => ! empty($profile->file_nib) ? 'emerald' : 'slate',
                'download_url' => ! empty($profile->file_nib) ? route('mitra.document.download', 'nib') : null,
            ],
            [
                'type' => 'sewa',
                'name' => 'Surat Perjanjian Sewa Lahan',
                'description' => 'Bukti penguasaan atau hak sewa lahan tempat berdirinya fasilitas.',
                'path' => $profile->file_sewa_lahan,
                'status' => ! empty($profile->file_sewa_lahan) ? 'Terverifikasi' : 'Tidak Dilampirkan',
                'badge_color' => ! empty($profile->file_sewa_lahan) ? 'emerald' : 'slate',
                'download_url' => ! empty($profile->file_sewa_lahan) ? route('mitra.document.download', 'sewa') : null,
            ],
        ];
    }

    public function openEditModal(): void
    {
        $profile = $this->profile();
        $user = $this->user();

        $this->nama_usaha = (string) ($profile->nama_usaha ?? '');
        $this->bentuk_badan_usaha = (string) ($profile->bentuk_badan_usaha ?? 'Perorangan');
        $this->kota_operasional = (string) ($profile->kota_operasional ?? '');
        $this->deskripsi_usaha = (string) ($profile->deskripsi_usaha ?? '');
        $this->user_name = (string) ($user->name ?? '');
        $this->user_phone = (string) ($user->phone ?? '');

        $this->resetValidation();
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetValidation();
    }

    public function updateProfile(): void
    {
        $this->authorizeMitraApproved();

        $this->validate([
            'nama_usaha' => ['required', 'string', 'max:150'],
            'bentuk_badan_usaha' => ['required', 'string', 'max:50'],
            'kota_operasional' => ['required', 'string', 'max:100'],
            'deskripsi_usaha' => ['nullable', 'string', 'max:1000'],
            'user_name' => ['required', 'string', 'max:100'],
            'user_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $profile = $this->profile();
        $user = $this->user();

        $profile->update([
            'nama_usaha' => $this->nama_usaha,
            'bentuk_badan_usaha' => $this->bentuk_badan_usaha,
            'kota_operasional' => $this->kota_operasional,
            'deskripsi_usaha' => $this->deskripsi_usaha,
        ]);

        $user->update([
            'name' => $this->user_name,
            'phone' => $this->user_phone,
        ]);

        AuditLog::record(
            $user,
            'MitraProfile',
            $profile->id,
            'update_profil',
            null,
            null,
            'Mitra memperbarui informasi kontak dan data usaha.'
        );

        $this->showEditModal = false;
        $this->notify('Profil usaha Anda berhasil diperbarui!', 'success');
    }

    public function render(): View
    {
        return view('livewire.mitra.profile.profile-manager');
    }
}
