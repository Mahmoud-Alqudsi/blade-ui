{{--
|--------------------------------------------------------------------------
| ui/avatar — avatar
|--------------------------------------------------------------------------
| Props:
|   src    : image URL (initials are shown if the image fails to load)
|   name   : name used to generate initials; supports Arabic text
|   alt    : alternative text
|   size   : xs | sm | md | lg | xl
|   shape  : circle | square
|   status : online | busy | away | offline
| Example: <x-ui.avatar name="Sarah Ahmed" status="online" size="lg" />
--}}
@props(['src' => null, 'alt' => '', 'name' => null, 'size' => 'md', 'shape' => 'circle', 'status' => null])

@php
    $sizeClass = match ($size) {
        'xs' => 'size-6 text-[10px]',
        'sm' => 'size-8 text-xs',
        'lg' => 'size-12 text-base',
        'xl' => 'size-16 text-lg',
        default => 'size-10 text-sm',
    };

    $shapeClass = $shape === 'square' ? 'rounded-lg' : 'rounded-full';

    $statusClass = match ($status) {
        'online' => 'bg-emerald-500',
        'busy' => 'bg-red-500',
        'away' => 'bg-amber-500',
        'offline' => 'bg-slate-400',
        default => null,
    };

    $initials = $name
        ? collect(preg_split('/\s+/u', trim($name)))->filter()->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->implode('')
        : null;

    $label = $name ?: $alt;
@endphp

<span x-data="{ failed: false }" @if ($label) role="img" aria-label="{{ $label }}" @else aria-hidden="true" @endif
      {{ $attributes->class(['relative inline-flex shrink-0', $sizeClass]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="" x-show="!failed" x-on:error="failed = true"
             class="size-full object-cover {{ $shapeClass }}">
    @endif

    <span @if ($src) x-show="failed" x-cloak @endif aria-hidden="true"
          class="inline-flex size-full items-center justify-center font-semibold {{ $shapeClass }} bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">
        @if ($initials)
            {{ $initials }}
        @else
            <x-ui.icon name="user" size="size-1/2" />
        @endif
    </span>

    @if ($statusClass)
        <span class="absolute bottom-0 end-0 size-2.5 rounded-full ring-2 ring-white dark:ring-slate-900 {{ $statusClass }}" aria-hidden="true"></span>
    @endif
</span>
