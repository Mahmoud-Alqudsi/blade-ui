{{--
|--------------------------------------------------------------------------
| ui/button — button
|--------------------------------------------------------------------------
| Props:
|   variant     : primary | secondary | success | danger | outline | ghost
|   size        : xs | sm | md | lg
|   type        : button | submit
|   disabled    : disable the button
|   loading     : show a spinner and disable the button (server-controlled)
|   loadingWhen : Alpine expression for dynamic loading, e.g. loading-when="submitting"
|   block       : use full width
|   href        : render an anchor when provided
| Named slots: icon-left, icon-right (logical spacing mirrors automatically in RTL)
|
| Examples:
|   <x-ui.button variant="danger" size="sm">Delete</x-ui.button>
|   <x-ui.button type="submit" block loading-when="submitting">
|       <x-slot:icon-right><x-ui.icon name="lock" size="size-4" /></x-slot:icon-right>
|       Sign in
|   </x-ui.button>
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'loadingWhen' => null,
    'block' => false,
    'href' => null,
])

@php
    $inactive = $disabled || $loading;
    $tag = $href ? 'a' : 'button';

    $base = 'inline-flex items-center justify-center font-medium rounded-lg select-none whitespace-nowrap transition-colors duration-150 '
        .'focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 '
        .'disabled:cursor-not-allowed disabled:opacity-60 aria-disabled:pointer-events-none aria-disabled:opacity-60';

    $variantClass = match ($variant) {
        'secondary' => 'bg-slate-100 text-slate-900 hover:bg-slate-200 active:bg-slate-300 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700 dark:active:bg-slate-600',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 active:bg-emerald-800 focus-visible:ring-emerald-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 focus-visible:ring-red-500',
        'outline' => 'border border-slate-300 bg-transparent text-slate-700 hover:bg-slate-50 active:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800 dark:active:bg-slate-700',
        'ghost' => 'bg-transparent text-slate-700 hover:bg-slate-100 active:bg-slate-200 dark:text-slate-200 dark:hover:bg-slate-800 dark:active:bg-slate-700',
        default => 'bg-brand-600 text-white hover:bg-brand-700 active:bg-brand-800',
    };

    $sizeClass = match ($size) {
        'xs' => 'h-7 gap-1.5 px-2.5 text-xs',
        'sm' => 'h-8 gap-1.5 px-3 text-sm',
        'lg' => 'h-12 gap-2.5 px-6 text-base',
        default => 'h-10 gap-2 px-4 text-sm',
    };

    $spinnerSize = match ($size) {
        'xs' => 'xs',
        'lg' => 'md',
        default => 'sm',
    };

    // Attributes specific to each element: <a> or <button>.
    $tagAttributes = $href
        ? ['href' => $inactive ? null : $href, 'aria-disabled' => $inactive ? 'true' : null]
        : ['type' => $type, 'disabled' => $inactive];

    if ($loadingWhen) {
        $tagAttributes['x-bind:disabled'] = $loadingWhen;
        $tagAttributes['x-bind:aria-busy'] = "({$loadingWhen}) ? 'true' : 'false'";
    }

    $tagAttributes['aria-busy'] = $loading ? 'true' : null;

    $hasLeft = isset($iconLeft) && $iconLeft->isNotEmpty();
    $hasRight = isset($iconRight) && $iconRight->isNotEmpty();
@endphp

<{{ $tag }} {{ $attributes->merge($tagAttributes)->class([$base, $variantClass, $sizeClass, 'w-full' => $block]) }}>
    @if ($loading)
        <x-ui.spinner :size="$spinnerSize" />
    @elseif ($loadingWhen)
        <x-ui.spinner :size="$spinnerSize" x-show="{{ $loadingWhen }}" x-cloak />
    @endif

    @if ($hasLeft)
        <span class="shrink-0" aria-hidden="true" @if ($loadingWhen) x-show="!({{ $loadingWhen }})" @endif>{{ $iconLeft }}</span>
    @endif

    {{ $slot }}

    @if ($hasRight)
        <span class="shrink-0" aria-hidden="true">{{ $iconRight }}</span>
    @endif
</{{ $tag }}>
