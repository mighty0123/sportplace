<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mitra\MitraOnboardingRequest;
use App\Models\AuditLog;
use App\Models\MitraProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OnboardingController extends Controller
{
    /**
     * MTR-01 / FR-MITRA-001: Tampilkan formulir pendaftaran mitra.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $profile = $user->mitraProfile;

        if ($profile) {
            if ($profile->isApproved()) {
                return redirect()->route('mitra.dashboard');
            }

            return redirect()->route('mitra.status');
        }

        return view('mitra.onboarding', [
            'user' => $user,
            'profile' => null,
        ]);
    }

    /**
     * Simpan pengajuan pendaftaran mitra baru.
     */
    public function store(MitraOnboardingRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $storagePath = "mitra_documents/{$user->id}";

        // Simpan dokumen di disk privat (storage/app/private/mitra_documents/...)
        $data['file_ktp'] = $request->file('file_ktp')->store($storagePath, 'local');

        if ($request->hasFile('file_npwp')) {
            $data['file_npwp'] = $request->file('file_npwp')->store($storagePath, 'local');
        }

        if ($request->hasFile('file_nib')) {
            $data['file_nib'] = $request->file('file_nib')->store($storagePath, 'local');
        }

        if ($request->hasFile('file_sewa_lahan')) {
            $data['file_sewa_lahan'] = $request->file('file_sewa_lahan')->store($storagePath, 'local');
        }

        $profile = MitraProfile::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($data, [
                'status' => 'pending',
                'alasan_penolakan' => null,
            ])
        );

        // NFR-LOG-001 / BR-028: Audit Log
        AuditLog::record(
            $user,
            'MitraProfile',
            $profile->id,
            'pendaftaran',
            null,
            'pending',
            'Mitra mengirimkan formulir dan berkas legalitas awal.'
        );

        return redirect()->route('mitra.status')
            ->with('success', 'Pendaftaran mitra berhasil dikirimkan! Pengajuan Anda sedang dalam proses verifikasi.');
    }

    /**
     * MTR-02 / FR-MITRA-002: Tampilkan status verifikasi akun mitra.
     */
    public function showStatus(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $profile = $user->mitraProfile;

        if (! $profile) {
            return redirect()->route('mitra.onboarding.create')
                ->with('status', 'Silakan lengkapi formulir pendaftaran mitra terlebih dahulu.');
        }

        return view('mitra.status', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * MTR-12 / FR-MITRA-004: Edit berkas dan ajukan ulang jika ditolak.
     */
    public function editResubmit(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $profile = $user->mitraProfile;

        if (! $profile) {
            return redirect()->route('mitra.onboarding.create');
        }

        if (! $profile->isRejected()) {
            return redirect()->route('mitra.status');
        }

        return view('mitra.resubmit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Proses pengajuan ulang data & dokumen mitra (FR-MITRA-004).
     */
    public function resubmit(MitraOnboardingRequest $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->mitraProfile;

        if (! $profile || ! $profile->isRejected()) {
            return redirect()->route('mitra.status');
        }

        $data = $request->validated();
        $storagePath = "mitra_documents/{$user->id}";

        if ($request->hasFile('file_ktp')) {
            $data['file_ktp'] = $request->file('file_ktp')->store($storagePath, 'local');
        }

        if ($request->hasFile('file_npwp')) {
            $data['file_npwp'] = $request->file('file_npwp')->store($storagePath, 'local');
        }

        if ($request->hasFile('file_nib')) {
            $data['file_nib'] = $request->file('file_nib')->store($storagePath, 'local');
        }

        if ($request->hasFile('file_sewa_lahan')) {
            $data['file_sewa_lahan'] = $request->file('file_sewa_lahan')->store($storagePath, 'local');
        }

        $alasanLama = $profile->alasan_penolakan;

        $profile->update(array_merge($data, [
            'status' => 'pending',
            'alasan_penolakan' => null, // Reset status penolakan
        ]));

        // NFR-LOG-001 / BR-028: Catat perubahan di audit log
        AuditLog::record(
            $user,
            'MitraProfile',
            $profile->id,
            'ajukan_ulang',
            'rejected',
            'pending',
            "Mitra mengajukan perbaikan berkas setelah penolakan: '{$alasanLama}'"
        );

        return redirect()->route('mitra.status')
            ->with('success', 'Perbaikan berkas berhasil dikirimkan ulang! Pengajuan Anda akan segera ditinjau kembali.');
    }

    /**
     * Unduh / lihat berkas legalitas dari disk privat terproteksi.
     */
    public function downloadDocument(Request $request, string $type): StreamedResponse
    {
        $user = $request->user();
        $profile = $user->mitraProfile;

        if (! $profile) {
            abort(404);
        }

        $column = match ($type) {
            'ktp' => 'file_ktp',
            'npwp' => 'file_npwp',
            'nib' => 'file_nib',
            'sewa' => 'file_sewa_lahan',
            default => null,
        };

        if (! $column || empty($profile->{$column})) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        $path = $profile->{$column};

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('local')->response($path);
    }
}
