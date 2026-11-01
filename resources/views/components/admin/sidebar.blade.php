<aside class="fixed inset-y-0 left-0 z-40 flex w-70 flex-col gap-5 bg-primary-950 px-3 py-5 text-neutral-200
            transition-transform duration-200 motion-reduce:transition-none
            md:w-rail md:translate-x-0 md:items-center md:px-0
            lg:w-sidebar lg:items-stretch lg:px-3"
        :class="menuOpen ? 'translate-x-0' : '-translate-x-full'"
        aria-label="Menu navigasi">

    <div class="flex items-center justify-between px-2 md:px-0 lg:px-2">
        <a href="{{ route('admin.dashboard') }}" class="block">
            <span class="block overflow-hidden md:w-9 lg:w-auto">
                <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-9 max-w-none">
            </span>
        </a>
        <button type="button" @click="menuOpen = false" aria-label="Tutup menu"
                class="grid size-11 place-items-center rounded-lg hover:bg-primary-800 md:hidden">
            <x-ui.icon name="x" />
        </button>
    </div>

    <nav class="flex flex-col gap-0.5 md:w-full md:items-center lg:items-stretch">
        @foreach (config('admin.nav') as $group)
            @if ($group['label'])
                <p class="px-2.5 pb-1 pt-3 text-xs text-neutral-400 md:hidden lg:block">{{ $group['label'] }}</p>
                <hr class="hidden border-primary-800 md:my-2 md:block md:w-6 lg:hidden">
            @endif
            @foreach ($group['items'] as $item)
                <x-admin.nav-item :item="$item" :badge="$navCounts[$item['key'] ?? ''] ?? null" />
            @endforeach
        @endforeach
    </nav>
</aside>