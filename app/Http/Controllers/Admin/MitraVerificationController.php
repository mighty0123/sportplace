<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Admin\MitraAlreadyProcessedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveMitraRequest;
use App\Http\Requests\Admin\RejectMitraRequest;
use App\Models\AuditLog;
use App\Models\MitraProfile;
use App\Services\Admin\MitraVerificationService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MitraVerificationController extends Controller
{
    private const DOCUMENT_LABELS = [
        'ktp'  => ['KTP', true],
        'npwp' => ['NPWP', false],
        'nib'  => ['NIB', false],
        'sewa' => ['Sewa lahan', false],
    ];

    public function __construct(private MitraVerificationService $service)
    {
    }

    public function show(MitraProfile $mitra): View
    {
        Gate::authorize('review', $mitra);

        $mitra->load(['user', 'reviewer']);

        return view('admin.verifikasi.show', [
            'mitra'     => $mitra,
            'documents' => $this->documents($mitra),
            'history'   => $this->service->history($mitra),
        ]);
    }

    public function approve(ApproveMitraRequest $request, MitraProfile $mitra): RedirectResponse
    {
        try {
            $this->service->approve($mitra, $request->user(), $request->validated('checklist'));
        } catch (MitraAlreadyProcessedException $e) {
            return $this->backToList()->with('error', $e->getMessage());
        }

        return $this->backToList()->with('success', "Mitra {$this->nameOf($mitra)} disetujui.");
    }

    public function reject(RejectMitraRequest $request, MitraProfile $mitra): RedirectResponse
    {
        try {
            $this->service->reject(
                $mitra,
                $request->user(),
                $request->validated('alasan_penolakan'),
                $request->validated('checklist') ?? [],
            );
        } catch (MitraAlreadyProcessedException $e) {
            return $this->backToList()->with('error', $e->getMessage());
        }

        return $this->backToList()->with('success', "Pendaftaran {$this->nameOf($mitra)} ditolak.");
    }

    /** Membuka berkas legalitas dari disk privat. Hanya admin; setiap pembukaan dicatat. */
    public function document(Request $request, MitraProfile $mitra, string $type): StreamedResponse
    {
        Gate::authorize('review', $mitra);

        $path = $mitra->documentPath($type);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_if(! $path || ! $disk->exists($path), 404, 'Berkas tidak ditemukan.');

        AuditLog::record($request->user(), 'MitraProfile', $mitra->id, 'lihat_dokumen', null, null, "Membuka berkas {$type}.");

        return $disk->response($path, null, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, no-store',
        ]);
    }

    /**
     * NIK lengkap tidak ikut di HTML halaman; baru dikirim saat admin menekan "Tampilkan",
     * dan setiap permintaan dicatat di audit log.
     */
    public function revealNik(Request $request, MitraProfile $mitra): JsonResponse
    {
        Gate::authorize('review', $mitra);

        AuditLog::record($request->user(), 'MitraProfile', $mitra->id, 'lihat_nik', null, null, 'Menampilkan NIK lengkap.');

        return response()->json(['nik' => $mitra->no_ktp])->header('Cache-Control', 'no-store');
    }

    /** Data berkas untuk tampilan: tersedia atau tidak, jenis (gambar/PDF), alamat rute privat, ukuran. */
    private function documents(MitraProfile $mitra): Collection
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        return collect(self::DOCUMENT_LABELS)->map(function (array $meta, string $key) use ($mitra, $disk) {
            $path      = $mitra->documentPath($key);
            $available = $path && $disk->exists($path);

            return [
                'key'       => $key,
                'label'     => $meta[0],
                'required'  => $meta[1],
                'available' => (bool) $available,
                'url'       => $available ? route('admin.verifikasi.document', [$mitra, $key]) : null,
                'type'      => $available && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf' ? 'pdf' : 'image',
                'size'      => $available ? Number::fileSize($disk->size($path), 1) : null,
            ];
        })->values();
    }

    private function nameOf(MitraProfile $mitra): string
    {
        return $mitra->nama_usaha ?: "#{$mitra->id}";
    }

    private function backToList(): RedirectResponse
    {
        return redirect()->route(
            Route::has('admin.verifikasi.index') ? 'admin.verifikasi.index' : 'admin.dashboard'
        );
    }
}