<header class="sticky top-0 z-20 flex h-14 items-center justify-between bg-primary-950 pl-1 pr-3 md:hidden">
    <button type="button" @click="menuOpen = true" aria-label="Buka menu" :aria-expanded="menuOpen"
            class="grid size-11 place-items-center rounded-lg text-neutral-200 hover:bg-primary-800">
        <x-ui.icon name="menu" />
    </button>
    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-7.5">
    <div class="grid size-9 place-items-center rounded-full bg-primary-100 text-[13px] font-semibold text-primary-700">NP</div>
</header>