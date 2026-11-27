{{--
|--------------------------------------------------------------------------
| ui/skeleton — loading placeholder
|--------------------------------------------------------------------------
| Props:
|   shape : rect | circle | text
|   lines : number of lines for shape="text" (the last line is shorter)
| Set dimensions with classes such as class="h-24 w-full" or class="size-12" for a circle.
| Example: <x-ui.skeleton shape="text" :lines="3" />
--}}
@props(['shape' => 'rect', 'lines' => 1])

@php
    $base = 'animate-pulse bg-slate-200 motion-reduce:animate-none dark:bg-slate-800';
@endphp

@if ($shape === 'text')
    <div aria-hidden="true" {{ $attributes->twClass('w-full space-y-2.5') }}>
        @for ($i = 1; $i <= $lines; $i++)
            <div @class([$base, 'h-3.5 rounded-md', 'w-3/4' => $i === $lines && $lines > 1, 'w-full' => ! ($i === $lines && $lines > 1)])></div>
        @endfor
    </div>
@else
    <div aria-hidden="true" {{ $attributes->twClass([$base, $shape === 'circle' ? 'size-10 rounded-full' : 'h-20 w-full rounded-xl']) }}></div>
@endif
