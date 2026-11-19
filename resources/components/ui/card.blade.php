{{--
|--------------------------------------------------------------------------
| ui/card — card
|--------------------------------------------------------------------------
| Props:
|   hoverable : increase the shadow on hover
|   padding   : none | sm | md | lg (applies to the card body)
| Slots: header, footer, and the default $slot content
| Example:
|   <x-ui.card hoverable padding="lg">
|       <x-slot:header>Heading</x-slot:header>
|       Content
|       <x-slot:footer>Footer</x-slot:footer>
|   </x-ui.card>
--}}
@props(['hoverable' => false, 'padding' => 'md'])

@php
    $bodyPadding = match ($padding) {
        'none' => '',
        'sm' => 'p-3',
        'lg' => 'p-8',
        default => 'p-5',
    };

    $edgePadding = match ($padding) {
        'sm' => 'px-3 py-2.5',
        'lg' => 'px-8 py-5',
        default => 'px-5 py-3.5',
    };

    $hasHeader = isset($header) && $header->isNotEmpty();
    $hasFooter = isset($footer) && $footer->isNotEmpty();
@endphp

<div {{ $attributes->class([
    'rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-sm',
    'dark:border-slate-800 dark:bg-slate-900 dark:text-slate-100',
    'transition-shadow duration-200 hover:shadow-lg motion-reduce:transition-none' => $hoverable,
]) }}>
    @if ($hasHeader)
        <div class="{{ $edgePadding }} border-b border-slate-200 font-semibold dark:border-slate-800">{{ $header }}</div>
    @endif

    <div class="{{ $bodyPadding }}">{{ $slot }}</div>

    @if ($hasFooter)
        <div class="{{ $edgePadding }} rounded-b-2xl border-t border-slate-200 bg-slate-50 text-sm dark:border-slate-800 dark:bg-slate-900/60">{{ $footer }}</div>
    @endif
</div>
