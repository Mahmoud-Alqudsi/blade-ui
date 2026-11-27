{{-- Accordion item used inside ui/accordion. Props: title, open. --}}
@props(['title', 'open' => false])

@php
    $uid = 'acc-'.\Illuminate\Support\Str::random(6);
@endphp

<div x-data="{ id: @js($uid) }" @if ($open) x-init="toggle(id)" @endif {{ $attributes }}>
    <h3>
        <button type="button" id="{{ $uid }}-button" aria-controls="{{ $uid }}-panel"
                x-bind:aria-expanded="isOpen(id) ? 'true' : 'false'"
                x-on:click="toggle(id)"
                class="flex w-full items-center justify-between gap-4 px-5 py-4 text-start text-sm font-medium text-slate-900 transition-colors hover:bg-slate-50 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-500 dark:text-slate-100 dark:hover:bg-slate-800/50">
            {{ $title }}
            <x-blade-ui::ui.icon name="chevron-down" size="size-5" class="text-slate-500 transition-transform motion-reduce:transition-none dark:text-slate-400"
                       x-bind:class="isOpen(id) ? 'rotate-180' : ''" />
        </button>
    </h3>

    <div id="{{ $uid }}-panel" role="region" aria-labelledby="{{ $uid }}-button" x-show="isOpen(id)" x-cloak
         x-transition.opacity.duration.150ms
         class="px-5 pb-4 text-sm leading-relaxed text-slate-600 dark:text-slate-300">
        {{ $slot }}
    </div>
</div>
