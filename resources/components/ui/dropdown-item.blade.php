{{--
|--------------------------------------------------------------------------
| ui/dropdown-item — dropdown menu item
|--------------------------------------------------------------------------
| Props:
|   href   : render an anchor when provided; otherwise render a button
|   danger : use a danger color for destructive actions
| Example: <x-ui.dropdown-item href="/settings">Settings</x-ui.dropdown-item>
--}}
@props(['href' => null, 'danger' => false, 'type' => 'button'])

@php
    $tag = $href ? 'a' : 'button';
    $tone = $danger
        ? 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10'
        : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800';
@endphp

<{{ $tag }} role="menuitem" {{ $attributes->merge($href ? ['href' => $href] : ['type' => $type])->twClass([
    'flex w-full items-center gap-2 rounded-lg px-3 py-2 text-start text-sm transition-colors',
    'focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-500',
    $tone,
]) }}>{{ $slot }}</{{ $tag }}>
