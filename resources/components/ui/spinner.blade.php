{{--
|--------------------------------------------------------------------------
| ui/spinner — loading indicator
|--------------------------------------------------------------------------
| Props:
|   size  : xs | sm | md | lg (defaults to md)
|   label : accessible label (defaults to "Loading")
| Inherits the surrounding text color (currentColor).
| Example: <x-ui.spinner size="lg" class="text-brand-600" />
--}}
@props(['size' => 'md', 'label' => 'جارٍ التحميل'])

@php
    $sizeClass = match ($size) {
        'xs' => 'size-3',
        'sm' => 'size-4',
        'lg' => 'size-6',
        default => 'size-5',
    };
@endphp

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" role="status" aria-label="{{ $label }}"
     {{ $attributes->class(['animate-spin motion-reduce:animate-none', $sizeClass]) }}>
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
</svg>
