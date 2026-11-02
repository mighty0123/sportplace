@php
    $ex     = $exception ?? null;
    $status = $ex && method_exists($ex, 'getStatusCode') ? $ex->getStatusCode() : 400;
@endphp

<x-error.page :code="$status"
    title="Permintaan tidak dapat diproses"
    description="Server tidak bisa memproses permintaan yang dikirim."
    hint="Kembali ke halaman sebelumnya lalu ulangi. Kalau tetap gagal, hubungi tim teknis.">
    <x-slot:scene>
        <svg viewBox="0 0 300 250" fill="none" class="h-auto w-full"><path d="M24 125H80M220 125H276" stroke="#43e5c5" stroke-width="3" stroke-linecap="round"/><circle cx="150" cy="125" r="70" fill="none" stroke="#43e5c5" stroke-width="3"/><g transform="translate(150 125)"><g class="ball-roll"><circle r="26" fill="#e2ee58"/><path d="M-22.5 -13C-7.1 -4.7 -7.1 4.7 -22.5 13M22.5 -13C7.1 -4.7 7.1 4.7 22.5 13" fill="none" stroke="#fcfbf5" stroke-width="2.8" stroke-linecap="round"/></g></g></svg>
    </x-slot:scene>
</x-error.page>