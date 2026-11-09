@php
    $review = is_array($mitra->review_checklist) ? $mitra->review_checklist : [];
    $manual = $review['manual'] ?? null;
    $auto   = $review['auto'] ?? null;
    $words  = ['pass' => 'lolos', 'warn' => 'perlu perhatian', 'fail' => 'tidak lolos', 'na' => 'tidak berlaku'];
@endphp

<h2 class="text-h6 text-ink">Hasil keputusan</h2>

<div class="mt-2 space-y-3">
    @if ($mitra->isRejected() && $mitra->alasan_penolakan)
        <div class="rounded-md bg-danger-50 px-3 py-2 text-body-sm text-danger-900">{{ $mitra->alasan_penolakan }}</div>
    @endif

    @if ($manual)
        <ul class="space-y-1.5">
            @foreach (\App\Services\Admin\MitraVerificationService::CHECKLIST as $key => $label)
                @continue(! array_key_exists($key, $manual))
                <li class="flex items-start gap-2 text-body-sm {{ $manual[$key] ? 'text-ink' : 'text-ink-muted' }}">
                    <x-ui.icon :name="$manual[$key] ? 'check-circle' : 'x-circle'" class="{{ $manual[$key] ? 'text-success-700' : 'text-danger-700' }}" />
                    <span>{{ $label }}<span class="sr-only">{{ $manual[$key] ? ', dicentang' : ', tidak dicentang' }}</span></span>
                </li>
            @endforeach
        </ul>
    @endif

    @if ($auto)
        <p class="text-caption text-ink-muted">
            Pemeriksaan otomatis saat diputuskan:
            {{ collect($auto)->countBy()->map(fn ($n, $s) => "{$n} " . ($words[$s] ?? $s))->implode(', ') }}.
        </p>
    @endif

    @if (! $manual && ! $auto)
        <p class="text-caption text-ink-muted">Diputuskan sebelum pencatatan hasil pemeriksaan ada.</p>
    @endif

    <p class="text-caption text-ink-body">
        {{ $mitra->isApproved() ? 'Mitra dapat membuat venue dan lapangan.' : 'Menunggu mitra memperbaiki berkas dan mengajukan ulang.' }}
        Keputusan sudah final, jadi tombol tidak tersedia.
    </p>
</div>