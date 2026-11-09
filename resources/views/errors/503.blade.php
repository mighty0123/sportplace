@php
    $ex    = $exception ?? null;
    $retry = $retryAfter ?? ($ex && method_exists($ex, 'getHeaders') ? ($ex->getHeaders()['Retry-After'] ?? null) : null);
    $hint  = $retry ? 'Perkiraan kembali dalam sekitar ' . ceil($retry / 60) . ' menit.' : 'Silakan coba lagi beberapa saat lagi.';
@endphp

<x-error.page code="503"
    title="SportPlace sedang dalam perawatan"
    description="Kami sedang memperbarui sistem supaya lebih baik. Layanan akan kembali sebentar lagi."
    :hint="$hint">
    <x-slot:scene>
        <svg viewBox="0 0 557 250" fill="none" class="h-auto w-full"><g fill="none" stroke="#43e5c5" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"><use href="#g5" x="24" y="190"/><use href="#g3" x="316" y="190"/></g><circle cx="235" cy="117" r="71.5" fill="none" stroke="#43e5c5" stroke-width="3"/><rect x="218" y="97" width="12" height="40" rx="5" fill="#53a1ee"/><rect x="240" y="97" width="12" height="40" rx="5" fill="#53a1ee"/><ellipse cx="483" cy="190" rx="22" ry="4.5" fill="#000" opacity=".28"/><g transform="translate(483 164)"><g class="ball-roll"><circle r="22" fill="#e2ee58"/><path d="M-19 -11C-6 -4 -6 4 -19 11M19 -11C6 -4 6 4 19 11" fill="none" stroke="#fcfbf5" stroke-width="2.4" stroke-linecap="round"/></g></g></svg>
    </x-slot:scene>

    <x-slot:actions>
        <x-error.button icon="refresh" onclick="location.reload()">Muat ulang halaman</x-error.button>
    </x-slot:actions>
</x-error.page>