{{--
|--------------------------------------------------------------------------
| ui/drawer — side panel
|--------------------------------------------------------------------------
| Props:
|   name      : unique required name
|   title     : panel heading
|   side      : end (default) | start — logical sides mirror in RTL
|   width     : sm | md | lg
|   closeable : close by clicking outside, pressing Esc, or using the close button
| Slots: default content and footer
| Open: $dispatch('open-drawer-filters'); close: $dispatch('close-drawer-filters')
| Traps focus and locks page scrolling, like the modal component.
--}}
@props(['name', 'title' => null, 'side' => 'end', 'width' => 'md', 'closeable' => true, 'show' => false, 'closeLabel' => null, 'label' => null])

@php
    $closeLabel ??= __('blade-ui::messages.close');
@endphp

@php
    $widthClass = match ($width) {
        'sm' => 'max-w-xs',
        'lg' => 'max-w-lg',
        default => 'max-w-sm',
    };

    $sideClass = $side === 'start' ? 'start-0' : 'end-0';
    $hiddenClass = $side === 'start'
        ? 'ltr:-translate-x-full rtl:translate-x-full'
        : 'ltr:translate-x-full rtl:-translate-x-full';

    $titleId = "drawer-{$name}-title";
    $hasFooter = isset($footer) && $footer->isNotEmpty();
@endphp

<div x-data="{ show: @js($show), closeable: @js($closeable) }"
     x-show="show" x-cloak
     x-trap.noscroll="show"
     x-on:open-drawer-{{ $name }}.window="show = true"
     x-on:close-drawer-{{ $name }}.window="show = false"
     x-on:keydown.escape.window="if (closeable) show = false"
     class="fixed inset-0 z-50"
     role="dialog" aria-modal="true"
     @if ($title) aria-labelledby="{{ $titleId }}" @else aria-label="{{ $label ?? $name }}" @endif>

    <div x-show="show" aria-hidden="true"
         x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         x-on:click="if (closeable) show = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div x-show="show"
         x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
         x-transition:enter-start="{{ $hiddenClass }}" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
         x-transition:leave-start="translate-x-0" x-transition:leave-end="{{ $hiddenClass }}"
         {{ $attributes->twClass(['fixed inset-y-0 flex w-full flex-col bg-white shadow-xl dark:bg-slate-900 dark:text-slate-100', $widthClass, $sideClass]) }}>

        <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
            @if ($title)
                <h2 id="{{ $titleId }}" class="text-lg font-semibold">{{ $title }}</h2>
            @endif

            @if ($closeable)
                <button type="button" x-on:click="show = false" aria-label="{{ $closeLabel }}"
                        class="-m-1.5 ms-auto rounded-md p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:hover:bg-slate-800 dark:hover:text-slate-200">
                    <x-blade-ui::ui.icon name="x-mark" />
                </button>
            @endif
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-4 text-sm text-slate-600 dark:text-slate-300">{{ $slot }}</div>

        @if ($hasFooter)
            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-800 dark:bg-slate-900/60">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
