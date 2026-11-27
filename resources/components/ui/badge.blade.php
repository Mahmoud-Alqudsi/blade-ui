{{--
|--------------------------------------------------------------------------
| ui/badge — badge
|--------------------------------------------------------------------------
| Props:
|   variant : neutral | brand | success | warning | danger | info
|   size    : sm | md
|   dot     : show a colored dot before the label
| Example: <x-ui.badge variant="success" dot>Active</x-ui.badge>
--}}
@props(['variant' => 'neutral', 'size' => 'md', 'dot' => false])

@php
    $variantClass = match ($variant) {
        'brand' => 'bg-brand-50 text-brand-700 ring-brand-600/20 dark:bg-brand-500/10 dark:text-brand-300 dark:ring-brand-400/30',
        'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-400/30',
        'warning' => 'bg-amber-50 text-amber-800 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-400/30',
        'danger' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-300 dark:ring-red-400/30',
        'info' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-300 dark:ring-sky-400/30',
        default => 'bg-slate-100 text-slate-700 ring-slate-500/20 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-400/20',
    };

    $sizeClass = $size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-2.5 py-1 text-xs';
@endphp

<span {{ $attributes->twClass(['inline-flex items-center gap-1.5 rounded-full font-medium ring-1 ring-inset', $variantClass, $sizeClass]) }}>
    @if ($dot)
        <span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
