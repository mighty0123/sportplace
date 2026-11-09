@php
    $verbs = ['pendaftaran' => 'Mendaftar', 'ajukan_ulang' => 'Mengajukan ulang', 'setujui' => 'Disetujui', 'tolak' => 'Ditolak'];
@endphp

<h2 class="text-h6 text-ink">Riwayat</h2>

@if ($history->isEmpty())
    <p class="mt-2 text-body-sm text-ink-muted">Belum ada riwayat tercatat.</p>
@else
    <ol class="mt-3">
        @foreach ($history as $log)
            <li class="border-l-2 border-line pb-4 pl-4 last:pb-0">
                <p class="text-body-sm text-ink">
                    <span class="font-medium">{{ $verbs[$log->aksi] ?? \Illuminate\Support\Str::headline($log->aksi) }}</span>
                    <span class="text-ink-muted">oleh {{ $log->user?->name ?? 'sistem' }}</span>
                </p>
                <p class="text-caption text-ink-muted">{{ $log->created_at->locale('id')->translatedFormat('j M Y, H.i') }}</p>
                @if ($log->catatan)
                    <p class="mt-0.5 text-caption text-ink-body">{{ $log->catatan }}</p>
                @endif
            </li>
        @endforeach
    </ol>
@endif