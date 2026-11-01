@php
    $messages = array_filter([
        'success' => session('success'),
        'error'   => session('error'),
        'info'    => session('status'),
    ]);

    $banners = array_intersect_key($messages, ['error' => true]);
    $toasts  = array_diff_key($messages, ['error' => true]);
@endphp

@foreach ($banners as $type => $text)
    <x-ui.alert :type="$type" class="mb-4">{{ $text }}</x-ui.alert>
@endforeach

@if ($toasts)
    <div class="pointer-events-none fixed inset-x-4 top-16 z-50 flex flex-col gap-2
                md:inset-x-auto md:bottom-6 md:right-6 md:top-auto md:w-96">
        @foreach ($toasts as $type => $text)
            <div x-data="{ show: true, timer: null,
                        start() { this.timer = setTimeout(() => this.show = false, 6000) },
                        stop() { clearTimeout(this.timer) } }"
                x-init="start()" x-show="show"
                x-transition:enter="transition duration-200 ease-out motion-reduce:transition-none"
                x-transition:enter-start="translate-y-2 opacity-0"
                x-transition:leave="transition duration-150 ease-in motion-reduce:transition-none"
                x-transition:leave-end="opacity-0"
                @mouseenter="stop()" @mouseleave="start()" @focusin="stop()" @focusout="start()"
                class="pointer-events-auto"
            >
                <x-ui.alert :type="$type" :standalone="false" class="shadow-float">{{ $text }}</x-ui.alert>
            </div>
        @endforeach
    </div>
@endif