{{--
|--------------------------------------------------------------------------
| ui/steps — progress indicator for multi-step flows
|--------------------------------------------------------------------------
| Props:
|   steps          : array of titles or items with 'title' and optional 'description' keys
|   current        : current step number (starts at 1)
|   label          : aria-label for the navigation element
|   completedLabel : accessible label for completed steps
| The current step has aria-current="step"; titles are hidden on small screens while numbers remain.
| Example: <x-ui.steps :steps="['Account', 'Profile', 'Confirmation']" :current="2" />
--}}
@props(['steps' => [], 'current' => 1, 'label' => null, 'completedLabel' => null])

@php
    $label ??= __('blade-ui::messages.steps');
    $completedLabel ??= __('blade-ui::messages.completed');
@endphp

@php
    $items = collect($steps)->map(fn ($step) => is_array($step) ? $step : ['title' => $step])->values();
    $current = (int) $current;
@endphp

<nav aria-label="{{ $label }}" {{ $attributes }}>
    <ol class="flex items-center">
        @foreach ($items as $index => $step)
            @php
                $number = $index + 1;
                $state = $number < $current ? 'complete' : ($number === $current ? 'current' : 'upcoming');
            @endphp

            <li @class(['flex items-center', 'flex-1' => ! $loop->last]) @if ($state === 'current') aria-current="step" @endif>
                <span class="flex items-center gap-3">
                    <span @class([
                        'inline-flex size-9 shrink-0 items-center justify-center rounded-full text-sm font-semibold transition-colors',
                        'bg-brand-600 text-white' => $state === 'complete',
                        'border-2 border-brand-600 bg-white text-brand-700 dark:bg-slate-900 dark:text-brand-300' => $state === 'current',
                        'border-2 border-slate-300 text-slate-500 dark:border-slate-700 dark:text-slate-400' => $state === 'upcoming',
                    ])>
                        @if ($state === 'complete')
                            <x-blade-ui::ui.icon name="check" size="size-5" stroke="2.5" />
                            <span class="sr-only">{{ $completedLabel }}</span>
                        @else
                            {{ $number }}
                        @endif
                    </span>

                    <span class="hidden text-sm sm:block">
                        <span @class([
                            'block font-medium',
                            'text-slate-900 dark:text-slate-100' => $state !== 'upcoming',
                            'text-slate-500 dark:text-slate-400' => $state === 'upcoming',
                        ])>{{ $step['title'] }}</span>
                        @if (! empty($step['description']))
                            <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $step['description'] }}</span>
                        @endif
                    </span>
                </span>

                @unless ($loop->last)
                    <span aria-hidden="true" @class([
                        'mx-3 h-0.5 flex-1 rounded-full',
                        'bg-brand-600' => $number < $current,
                        'bg-slate-200 dark:bg-slate-700' => $number >= $current,
                    ])></span>
                @endunless
            </li>
        @endforeach
    </ol>
</nav>
