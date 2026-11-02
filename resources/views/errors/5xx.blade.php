@php
    $ex     = $exception ?? null;
    $status = $ex && method_exists($ex, 'getStatusCode') ? $ex->getStatusCode() : 500;
@endphp

<x-error.page :code="$status"
    title="Layanan sedang bermasalah"
    description="Server tidak dapat menyelesaikan permintaanmu saat ini. Ini bukan kesalahanmu."
    hint="Coba lagi beberapa saat lagi. Kalau terus berulang, hubungi tim teknis.">
    <x-slot:scene>
        <svg viewBox="0 0 300 250" fill="none" class="h-auto w-full"><path d="M24 125H80M220 125H276" stroke="#43e5c5" stroke-width="3" stroke-linecap="round"/><circle cx="150" cy="125" r="70" fill="none" stroke="#43e5c5" stroke-width="3"/><g transform="translate(150 125)"><g class="ball-roll"><circle r="26" fill="#e2ee58"/><path d="M-22.5 -13C-7.1 -4.7 -7.1 4.7 -22.5 13M22.5 -13C7.1 -4.7 7.1 4.7 22.5 13" fill="none" stroke="#fcfbf5" stroke-width="2.8" stroke-linecap="round"/></g></g></svg>
    </x-slot:scene>

    <x-slot:actions>
        <x-error.button icon="refresh" onclick="location.reload()">Muat ulang halaman</x-error.button>
        <x-error.button variant="ghost" :href="url('/')">Ke beranda</x-error.button>
    </x-slot:actions>
</x-error.page>