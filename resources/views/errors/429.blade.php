@php
    $ex    = $exception ?? null;
    $retry = $ex && method_exists($ex, 'getHeaders') ? (int) ($ex->getHeaders()['Retry-After'] ?? 30) : 30;
    $retry = max(1, min($retry, 300));
@endphp

<x-error.page code="429"
    title="Terlalu banyak permintaan"
    description="Kamu mengirim permintaan terlalu cepat dalam waktu singkat. Tunggu sebentar, lalu coba lagi.">
    <x-slot:scene>
        <svg viewBox="0 0 579 250" fill="none" class="h-auto w-full"><path d="M410.6 118h-12" stroke="#e2ee58" stroke-width="3" stroke-linecap="round" opacity=".28"/><path d="M450.6 118h-12" stroke="#e2ee58" stroke-width="3" stroke-linecap="round" opacity=".49"/><path d="M490.6 118h-12" stroke="#e2ee58" stroke-width="3" stroke-linecap="round" opacity=".7"/><g fill="none" stroke="#43e5c5" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"><use href="#g4" x="24" y="190"/><use href="#g2" x="165.4" y="190"/><use href="#g9" x="285.6" y="190"/></g><g transform="translate(442.6 118)" opacity=".4"><circle r="19" fill="#e2ee58"/><path d="M-16.4 -9.5C-5.2 -3.5 -5.2 3.5 -16.4 9.5M16.4 -9.5C5.2 -3.5 5.2 3.5 16.4 9.5" fill="none" stroke="#fcfbf5" stroke-width="2.1" stroke-linecap="round"/></g><g transform="translate(482.6 118)" opacity=".7"><circle r="19" fill="#e2ee58"/><path d="M-16.4 -9.5C-5.2 -3.5 -5.2 3.5 -16.4 9.5M16.4 -9.5C5.2 -3.5 5.2 3.5 16.4 9.5" fill="none" stroke="#fcfbf5" stroke-width="2.1" stroke-linecap="round"/></g><g transform="translate(522.6 118)"><g class="ball-roll"><circle r="19" fill="#e2ee58"/><path d="M-16.4 -9.5C-5.2 -3.5 -5.2 3.5 -16.4 9.5M16.4 -9.5C5.2 -3.5 5.2 3.5 16.4 9.5" fill="none" stroke="#fcfbf5" stroke-width="2.1" stroke-linecap="round"/></g></g></svg>
    </x-slot:scene>

    <x-slot:actions>
        <x-error.button id="retry" icon="refresh" disabled>
            <span id="retry-label">Coba lagi dalam <span id="retry-count">{{ $retry }}</span> detik</span>
        </x-error.button>
        <noscript><p class="text-body-sm text-primary-200">Tunggu {{ $retry }} detik, lalu muat ulang halaman.</p></noscript>
    </x-slot:actions>
</x-error.page>

@push('scripts')
<script>
    (function () {
        var left = {{ $retry }};
        var btn = document.getElementById('retry');
        var count = document.getElementById('retry-count');
        var timer = setInterval(function () {
            left -= 1;
            if (left > 0) { count.textContent = left; return; }
            clearInterval(timer);
            document.getElementById('retry-label').textContent = 'Coba lagi';
            btn.disabled = false;
            btn.addEventListener('click', function () { location.reload(); });
        }, 1000);
    })();
</script>
@endpush