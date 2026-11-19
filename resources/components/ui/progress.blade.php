{{--
|--------------------------------------------------------------------------
| ui/progress — progress bar
|--------------------------------------------------------------------------
| Props:
|   value, max      : current value and maximum (defaults to 0 / 100)
|   variant         : brand | success | warning | danger
|   size            : sm | md | lg
|   label           : text shown above the bar and used as its accessible name
|   showValue       : display the percentage
|   indeterminate   : loading state with no known percentage
| Example: <x-ui.progress :value="65" label="Profile completion" show-value variant="success" />
--}}
@props(['value' => 0, 'max' => 100, 'variant' => 'brand', 'size' => 'md', 'label' => null, 'showValue' => false, 'indeterminate' => false])

@php
    $percent = $max > 0 ? (int) min(100, max(0, round($value / $max * 100))) : 0;

    $barClass = match ($variant) {
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-red-500',
        default => 'bg-brand-600',
    };

    $heightClass = match ($size) {
        'sm' => 'h-1.5',
        'lg' => 'h-3',
        default => 'h-2',
    };

    $labelId = 'progress-'.\Illuminate\Support\Str::random(6);
@endphp

<div {{ $attributes->class('w-full') }}>
    @if ($label || $showValue)
        <div class="mb-1.5 flex items-center justify-between text-sm">
            @if ($label)
                <span id="{{ $labelId }}" class="font-medium text-slate-700 dark:text-slate-200">{{ $label }}</span>
            @endif
            @if ($showValue && ! $indeterminate)
                <span class="text-slate-500 dark:text-slate-400">{{ $percent }}%</span>
            @endif
        </div>
    @endif

    <div role="progressbar" aria-valuemin="0" aria-valuemax="{{ $max }}"
         @unless ($indeterminate) aria-valuenow="{{ $value }}" @endunless
         @if ($label) aria-labelledby="{{ $labelId }}" @endif
         class="w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800 {{ $heightClass }}">
        <div @class([
                'h-full rounded-full transition-[width] duration-300 motion-reduce:transition-none',
                $barClass,
                'w-1/3 animate-pulse motion-reduce:animate-none' => $indeterminate,
            ])
            @unless ($indeterminate) style="width: {{ $percent }}%" @endunless></div>
    </div>
</div>
