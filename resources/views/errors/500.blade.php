<x-error.page code="500"
    title="Ada gangguan di sisi kami"
    description="Terjadi kesalahan saat memproses permintaanmu. Ini bukan kesalahanmu, dan kejadiannya tercatat di sistem."
    :hint="'Coba muat ulang beberapa saat lagi. Kalau terus berulang, hubungi tim teknis dan sebutkan waktu kejadian: ' . now()->locale('id')->translatedFormat('j F Y, H.i') . '.'">
    <x-slot:scene>
        <svg viewBox="0 0 516 250" fill="none" class="h-auto w-full"><g fill="none" stroke="#43e5c5" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"><use href="#g5" x="24" y="190"/></g><circle cx="235" cy="117" r="71.5" fill="none" stroke="#43e5c5" stroke-width="3"/><circle cx="235" cy="117" r="4.5" fill="#43e5c5"/><circle cx="391" cy="117" r="71.5" fill="none" stroke="#ed645a" stroke-width="3"/><circle cx="391" cy="117" r="4.5" fill="#ed645a"/><path d="M441.7 53.2L451.6 40.6" stroke="#ed645a" stroke-width="4" stroke-linecap="round"/><path d="M464.4 81.6L478.8 74.6" stroke="#ed645a" stroke-width="4" stroke-linecap="round"/><path d="M472.5 117L488.5 117" stroke="#ed645a" stroke-width="4" stroke-linecap="round"/><g transform="translate(391 117)"><g class="ball-roll"><circle r="26" fill="#e2ee58"/><path d="M-22.5 -13C-7.1 -4.7 -7.1 4.7 -22.5 13M22.5 -13C7.1 -4.7 7.1 4.7 22.5 13" fill="none" stroke="#fcfbf5" stroke-width="2.8" stroke-linecap="round"/></g><path d="M-13 -19 -3 -5 -13 3 1 20" stroke="#06231d" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/></g></svg>
    </x-slot:scene>

    <x-slot:actions>
        <x-error.button icon="refresh" onclick="location.reload()">Muat ulang halaman</x-error.button>
        <x-error.button variant="ghost" :href="url('/')">Ke beranda</x-error.button>
    </x-slot:actions>
</x-error.page>