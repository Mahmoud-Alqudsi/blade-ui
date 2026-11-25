{{--
|--------------------------------------------------------------------------
| ui/modal — modal dialog
|--------------------------------------------------------------------------
| Props:
|   name      : unique required name used by open and close events
|   title     : dialog heading (automatically linked through aria-labelledby)
|   maxWidth  : sm | md | lg | xl
|   closeable : allow closing by clicking outside, pressing Esc, or using the close button
|   show      : open the dialog when the page loads
| Slots: default content and footer actions
| Open: <x-ui.button x-on:click="$dispatch('open-modal-confirm')">Open</x-ui.button>
| Close: $dispatch('close-modal-confirm')
| Traps focus inside the dialog and locks page scrolling (x-trap.noscroll).
--}}
@props(['name', 'title' => null, 'maxWidth' => 'md', 'closeable' => true, 'show' => false, 'closeLabel' => null])

@php
    $closeLabel ??= __('blade-ui::messages.close');
@endphp

@php
    $widthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        default => 'max-w-md',
    };

    $titleId = "modal-{$name}-title";
    $hasFooter = isset($footer) && $footer->isNotEmpty();
@endphp

<div x-data="{ show: @js($show), closeable: @js($closeable) }"
     x-show="show" x-cloak
     x-trap.noscroll="show"
     x-on:open-modal-{{ $name }}.window="show = true"
     x-on:close-modal-{{ $name }}.window="show = false"
     x-on:keydown.escape.window="if (closeable) show = false"
     class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto px-4 py-6 sm:items-center"
     role="dialog" aria-modal="true" @if ($title) aria-labelledby="{{ $titleId }}" @endif>

    {{-- Backdrop --}}
    <div x-show="show" aria-hidden="true"
         x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         x-on:click="if (closeable) show = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    {{-- Dialog panel --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
         x-transition:enter-start="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
         x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
         x-transition:leave-start="opacity-100 sm:scale-100" x-transition:leave-end="opacity-0 sm:scale-95"
         {{ $attributes->class(['relative w-full rounded-2xl bg-white shadow-xl dark:bg-slate-900 dark:text-slate-100', $widthClass]) }}>

        @if ($title || $closeable)
            <div class="flex items-start justify-between gap-4 px-6 pt-5">
                @if ($title)
                    <h2 id="{{ $titleId }}" class="text-lg font-semibold">{{ $title }}</h2>
                @endif

                @if ($closeable)
                    <button type="button" x-on:click="show = false" aria-label="{{ $closeLabel }}"
                            class="-m-1.5 ms-auto rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                        <x-ui.icon name="x-mark" />
                    </button>
                @endif
            </div>
        @endif

        <div class="px-6 py-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $slot }}</div>

        @if ($hasFooter)
            <div class="flex flex-wrap items-center justify-end gap-2 rounded-b-2xl border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/60">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
