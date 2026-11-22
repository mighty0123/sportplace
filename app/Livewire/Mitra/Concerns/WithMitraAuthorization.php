<?php

namespace App\Livewire\Mitra\Concerns;

use App\Models\BookingDetail;
use App\Models\Field;
use App\Models\MitraProfile;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Support\Facades\Auth;

trait WithMitraAuthorization
{
    /**
     * Dapatkan user mitra yang sedang login.
     */
    protected function mitraUser(): User
    {
        /** @var User|null $user */
        $user = property_exists($this, 'currentUser') && $this->currentUser instanceof User
            ? $this->currentUser
            : Auth::user();

        if (! $user || $user->role !== 'mitra') {
            abort(403, 'Akses terbatas untuk akun Mitra.');
        }

        return $user;
    }

    /**
     * Dapatkan profil mitra (bisa null jika belum melengkapi formulir onboarding MTR-01).
     */
    protected function mitraProfile(): ?MitraProfile
    {
        return $this->mitraUser()->mitraProfile;
    }

    /**
     * Pastikan akun mitra telah berstatus disetujui ('approved') oleh Admin (EC-17, BR-002).
     */
    protected function authorizeMitraApproved(): MitraProfile
    {
        $profile = $this->mitraProfile();

        if (! $profile) {
            abort(403, 'Profil mitra belum terdaftar. Silakan lengkapi formulir pendaftaran mitra terlebih dahulu.');
        }

        if ($profile->status !== 'approved') {
            abort(403, 'Akun mitra Anda belum disetujui (status: '.$profile->status.').');
        }

        return $profile;
    }

    /**
     * Pastikan venue adalah milik mitra yang sedang login (BR-023).
     */
    protected function ensureOwnsVenue(Venue $venue): void
    {
        $profile = $this->authorizeMitraApproved();

        if ($venue->mitra_id !== $profile->id) {
            abort(403, 'Anda tidak memiliki hak akses terhadap venue ini.');
        }
    }

    /**
     * Pastikan lapangan adalah milik mitra yang sedang login (BR-023).
     */
    protected function ensureOwnsField(Field $field): void
    {
        $profile = $this->authorizeMitraApproved();

        $venue = $field->venue;
        if (! $venue || $venue->mitra_id !== $profile->id) {
            abort(403, 'Anda tidak memiliki hak akses terhadap lapangan ini.');
        }
    }

    /**
     * Pastikan detail booking adalah milik mitra yang sedang login (BR-023).
     */
    protected function ensureOwnsBookingDetail(BookingDetail $detail): void
    {
        $profile = $this->authorizeMitraApproved();

        if ($detail->field?->venue?->mitra_id !== $profile->id) {
            abort(403, 'Anda tidak memiliki hak akses terhadap pesanan ini.');
        }
    }

    /**
     * Helper pemancar event notifikasi toast ke frontend Alpine/Livewire.
     */
    protected function notify(string $message, string $type = 'success'): void
    {
        $this->dispatch('notify', [
            'type' => $type,
            'message' => $message,
        ]);
    }
}
