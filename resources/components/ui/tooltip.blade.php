{{--
|--------------------------------------------------------------------------
| ui/tooltip — tooltip
|--------------------------------------------------------------------------
| Props:
|   text     : tooltip text
|   position : top | bottom | start | end (start/end follow the page direction)
| Appears on hover and keyboard focus, and is linked through aria-describedby.
| Example: <x-ui.tooltip text="Copy link" position="bottom"><x-ui.button>Copy</x-ui.button></x-ui.tooltip>
--}}
@props(['text', 'position' => 'top'])

@php
    $positionClass = match ($position) {
        'bottom' => 'top-full mt-2 start-1/2 -translate-x-1/2 rtl:translate-x-1/2',
        'start' => 'end-full me-2 top-1/2 -translate-y-1/2',
        'end' => 'start-full ms-2 top-1/2 -translate-y-1/2',
        default => 'bottom-full mb-2 start-1/2 -translate-x-1/2 rtl:translate-x-1/2',
    };

    $tipId = 'tip-'.\Illuminate\Support\Str::random(6);
@endphp

<span x-data="{ open: false }" class="relative inline-flex"
      aria-describedby="{{ $tipId }}"
      x-on:mouseenter="open = true" x-on:mouseleave="open = false"
      x-on:focusin="open = true" x-on:focusout="open = false"
      x-on:keydown.escape="open = false">
    {{ $slot }}

    <span id="{{ $tipId }}" role="tooltip" x-show="open" x-cloak
          x-transition.opacity.duration.100ms
          {{ $attributes->class(['pointer-events-none absolute z-40 whitespace-nowrap rounded-md bg-slate-900 px-2.5 py-1.5 text-xs font-medium text-white shadow-md dark:bg-slate-700', $positionClass]) }}>
        {{ $text }}
    </span>
</span>
