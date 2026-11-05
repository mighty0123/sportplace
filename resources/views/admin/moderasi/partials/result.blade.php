<h2 class="text-h6 text-ink">Hasil keputusan</h2>

<div class="mt-2 space-y-3">
    @if ($status === 'rejected' && $item->alasan_penolakan)
        <div class="rounded-md bg-danger-50 px-3 py-2 text-body-sm text-danger-900">{{ $item->alasan_penolakan }}</div>
    @endif

    <p class="text-body-sm text-ink">
        @switch($status)
            @case('aktif') {{ $type['label'] }} aktif dan tampil di pencarian. @break
            @case('rejected') Menunggu mitra memperbaiki dan mengajukan ulang. @break
            @default {{ $type['label'] }} berstatus {{ $status }}.
        @endswitch
    </p>
    <p class="text-caption text-ink-body">Keputusan sudah final, jadi tombol tidak tersedia.</p>
</div>