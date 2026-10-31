<x-ui.modal name="logout" title="Keluar dari akun?" description="Kamu perlu masuk lagi untuk membuka panel admin.">
    <x-slot:footer>
        <x-ui.button variant="neutral" type="button" class="w-full sm:w-auto"
            x-on:click="$dispatch('close-modal', 'logout')">
            Batal
        </x-ui.button>

        {{-- POST + @csrf: logout tidak bisa dipicu dari tautan atau gambar situs lain --}}
        <form method="POST" action="{{ route('logout') }}" class="contents">
            @csrf
            <x-ui.button type="submit" class="w-full sm:w-auto">Keluar</x-ui.button>
        </form>
    </x-slot:footer>
</x-ui.modal>