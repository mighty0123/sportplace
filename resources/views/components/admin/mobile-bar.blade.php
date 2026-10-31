<header class="sticky top-0 z-20 flex h-14 items-center justify-between bg-primary-950 pl-1 pr-3 md:hidden">
    <button type="button" @click="menuOpen = true" aria-label="Buka menu" :aria-expanded="menuOpen"
            class="grid size-11 place-items-center rounded-lg text-neutral-200 hover:bg-primary-800">
        <x-ui.icon name="menu" />
    </button>
    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}" class="h-7.5">
    <x-admin.account-menu compact dark />
</header>