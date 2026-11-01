@props(['name', 'title', 'description' => null])

<dialog x-data
        x-ref="dialog"
        x-on:open-modal.window="$event.detail === @js($name) && $refs.dialog.showModal()"
        x-on:close-modal.window="$event.detail === @js($name) && $refs.dialog.close()"
        x-on:click="$event.target === $refs.dialog && $refs.dialog.close()"
        aria-labelledby="modal-{{ $name }}-title"
        class="m-auto w-[calc(100%-2rem)] max-w-md rounded-lg border border-line bg-surface p-0 text-ink-body shadow-modal backdrop:bg-primary-950/50">
    <div class="p-5">
        <h2 id="modal-{{ $name }}-title" class="text-h4 text-ink">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 text-body-md text-ink-muted">{{ $description }}</p>
        @endif
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="flex flex-col-reverse gap-2 border-t border-line px-5 py-4 sm:flex-row sm:justify-end">
            {{ $footer }}
        </div>
    @endisset
</dialog>