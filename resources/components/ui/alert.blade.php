{{--
|--------------------------------------------------------------------------
| ui/alert — alert message
|--------------------------------------------------------------------------
| Props:
|   type        : info | success | warning | danger
|   title       : optional heading
|   dismissible : show a close button powered by Alpine.js
|   closeLabel  : accessible label for the close button
| danger and warning use role="alert"; other types use role="status".
| Example: <x-ui.alert type="success" title="Saved" dismissible>Your details were updated.</x-ui.alert>
--}}
@props(['type' => 'info', 'title' => null, 'dismissible' => false, 'closeLabel' => null])

@php
    $closeLabel ??= __('blade-ui::messages.close');
@endphp

@php
    $style = match ($type) {
        'success' => [
            'box' => 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100',
            'icon' => 'text-emerald-600 dark:text-emerald-400',
            'name' => 'check-circle',
        ],
        'warning' => [
            'box' => 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100',
            'icon' => 'text-amber-600 dark:text-amber-400',
            'name' => 'exclamation-triangle',
        ],
        'danger' => [
            'box' => 'border-red-200 bg-red-50 text-red-900 dark:border-red-500/30 dark:bg-red-500/10 dark:text-red-100',
            'icon' => 'text-red-600 dark:text-red-400',
            'name' => 'exclamation-circle',
        ],
        default => [
            'box' => 'border-sky-200 bg-sky-50 text-sky-900 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-100',
            'icon' => 'text-sky-600 dark:text-sky-400',
            'name' => 'information-circle',
        ],
    };

    $role = in_array($type, ['danger', 'warning'], true) ? 'alert' : 'status';
@endphp

<div @if ($dismissible)
         x-data="{ show: true }" x-show="show" x-cloak
         x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @endif
     role="{{ $role }}"
     {{ $attributes->class(['flex items-start gap-3 rounded-xl border p-4 text-sm', $style['box']]) }}>
    <x-ui.icon :name="$style['name']" :class="$style['icon']" />

    <div class="min-w-0 flex-1 space-y-1">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div class="leading-relaxed">{{ $slot }}</div>
    </div>

    @if ($dismissible)
        <button type="button" x-on:click="show = false" aria-label="{{ $closeLabel }}"
                class="-m-1.5 rounded-md p-1.5 opacity-70 transition hover:opacity-100 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-current">
            <x-ui.icon name="x-mark" size="size-4" />
        </button>
    @endif
</div>
