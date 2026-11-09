<x-error.page code="419"
    title="Sesi halaman sudah berakhir"
    description="Halaman ini terlalu lama terbuka, jadi demi keamanan pengirimanmu yang terakhir tidak diproses. Datanya belum tersimpan."
    hint="Muat ulang halaman, lalu ulangi pengisiannya.">
    <x-slot:scene>
        <svg viewBox="0 0 520 250" fill="none" class="h-auto w-full"><g fill="none" stroke="#43e5c5" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"><use href="#g4" x="24" y="190"/><use href="#g1" x="165.4" y="190"/><use href="#g9" x="246.6" y="190"/></g><circle cx="447.6" cy="112" r="42" fill="none" stroke="#43e5c5" stroke-width="3" opacity=".25"/><circle cx="447.6" cy="112" r="42" fill="none" stroke="#53a1ee" stroke-width="5" stroke-linecap="round" stroke-dasharray="190 263.9" transform="rotate(-90 447.6 112)"/><path d="M447.6 60V50" stroke="#53a1ee" stroke-width="5" stroke-linecap="round"/><g transform="translate(447.6 112)"><g class="ball-roll"><circle r="20" fill="#e2ee58"/><path d="M-17.3 -10C-5.5 -3.6 -5.5 3.6 -17.3 10M17.3 -10C5.5 -3.6 5.5 3.6 17.3 10" fill="none" stroke="#fcfbf5" stroke-width="2.2" stroke-linecap="round"/></g></g></svg>
    </x-slot:scene>

    <x-slot:actions>
        <x-error.button icon="refresh" onclick="location.reload()">Muat ulang halaman</x-error.button>
    </x-slot:actions>
</x-error.page>