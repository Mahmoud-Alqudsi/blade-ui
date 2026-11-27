{{--
|--------------------------------------------------------------------------
| ui/stat-card — statistic card
|--------------------------------------------------------------------------
| Props:
|   label       : metric name
|   value       : primary value
|   change      : change text (for example, "+12%")
|   trend       : up | down | neutral (controls the change color and arrow)
|   description : supporting text beside the change (for example, "Compared with last month")
|   icon        : optional icon name from ui/icon
| Example: <x-ui.stat-card label="Sales" value="12,500" change="+12%" trend="up" icon="arrow-up" />
--}}
@props(['label', 'value', 'change' => null, 'trend' => 'neutral', 'description' => null, 'icon' => null])

@php
    $trendClass = match ($trend) {
        'up' => 'text-emerald-700 dark:text-emerald-400',
        'down' => 'text-red-600 dark:text-red-400',
        default => 'text-slate-500 dark:text-slate-400',
    };

    $trendIcon = match ($trend) {
        'up' => 'arrow-up',
        'down' => 'arrow-down',
        default => null,
    };
@endphp

<x-blade-ui::ui.card {{ $attributes }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="truncate text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">{{ $value }}</p>
        </div>

        @if ($icon)
            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-300">
                <x-blade-ui::ui.icon :name="$icon" />
            </span>
        @endif
    </div>

    @if ($change || $description)
        <p class="mt-3 flex flex-wrap items-center gap-x-2 text-sm">
            @if ($change)
                <span class="inline-flex items-center gap-1 font-medium {{ $trendClass }}">
                    @if ($trendIcon)
                        <x-blade-ui::ui.icon :name="$trendIcon" size="size-4" stroke="2" />
                    @endif
                    {{ $change }}
                </span>
            @endif
            @if ($description)
                <span class="text-slate-500 dark:text-slate-400">{{ $description }}</span>
            @endif
        </p>
    @endif
</x-blade-ui::ui.card>
