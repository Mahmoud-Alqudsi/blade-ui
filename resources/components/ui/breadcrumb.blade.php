{{--
|--------------------------------------------------------------------------
| ui/breadcrumb — breadcrumb navigation
|--------------------------------------------------------------------------
| Props:
|   items : array of items [['label' => 'Home', 'href' => '/'], ['label' => 'Users']]
|           The last item is the current page (aria-current="page").
|   label : aria-label for the navigation element
| The separator automatically mirrors in RTL layouts.
| Example: <x-ui.breadcrumb :items="[['label' => 'Home', 'href' => '/'], ['label' => 'Users']]" />
--}}
@props(['items' => [], 'label' => null])

@php
    $label ??= __('blade-ui::messages.breadcrumb');
@endphp

<nav aria-label="{{ $label }}" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
        @foreach ($items as $item)
            <li class="inline-flex items-center gap-1.5">
                @if (! $loop->last && ! empty($item['href']))
                    <a href="{{ $item['href'] }}"
                       class="rounded-md transition-colors hover:text-slate-900 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:hover:text-slate-100">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span @if ($loop->last) aria-current="page" @endif
                          @class(['font-medium text-slate-900 dark:text-slate-100' => $loop->last])>{{ $item['label'] }}</span>
                @endif

                @unless ($loop->last)
                    <x-ui.icon name="chevron-right" size="size-4" class="rtl:rotate-180" />
                @endunless
            </li>
        @endforeach
    </ol>
</nav>
