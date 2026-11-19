{{--
|--------------------------------------------------------------------------
| ui/pagination — pagination (works with paginate() and simplePaginate())
|--------------------------------------------------------------------------
| Props:
|   paginator   : paginator instance
|   onEachSide  : number of page links around the current page (defaults to 1)
|   showSummary : show a range summary (only for paginate())
|   prevLabel, nextLabel, navLabel : translatable labels
| Example: <x-ui.pagination :paginator="$users" />
| To use it by default: Paginator::defaultView('vendor.pagination.brand') in AppServiceProvider.
--}}
@props([
    'paginator',
    'onEachSide' => 1,
    'showSummary' => true,
    'prevLabel' => 'السابق',
    'nextLabel' => 'التالي',
    'navLabel' => 'التنقل بين الصفحات',
])

@php
    $isLengthAware = $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $current = $paginator->currentPage();
    $entries = [];

    if ($isLengthAware) {
        $last = $paginator->lastPage();
        $numbers = collect(range(max(1, $current - $onEachSide), min($last, $current + $onEachSide)))
            ->push(1, $last)->unique()->sort()->values();

        $previous = null;
        foreach ($numbers as $number) {
            if ($previous !== null && $number - $previous > 1) {
                $entries[] = '...';
            }
            $entries[] = $number;
            $previous = $number;
        }
    }

    $itemBase = 'inline-flex size-9 items-center justify-center rounded-lg text-sm font-medium transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500';
    $linkClass = 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800';
    $disabledClass = 'cursor-not-allowed text-slate-400 opacity-60 dark:text-slate-600';
    $currentClass = 'bg-brand-600 text-white';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ $navLabel }}"
         {{ $attributes->class('flex flex-col items-center justify-between gap-3 sm:flex-row') }}>
        @if ($isLengthAware && $showSummary)
            <p class="text-sm text-slate-600 dark:text-slate-400">
                عرض {{ $paginator->firstItem() }} إلى {{ $paginator->lastItem() }} من {{ $paginator->total() }}
            </p>
        @else
            <span aria-hidden="true"></span>
        @endif

        <ul class="flex items-center gap-1">
            <li>
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ $prevLabel }}" class="{{ $itemBase }} {{ $disabledClass }}">
                        <x-ui.icon name="chevron-left" size="size-4" class="rtl:rotate-180" />
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ $prevLabel }}" class="{{ $itemBase }} {{ $linkClass }}">
                        <x-ui.icon name="chevron-left" size="size-4" class="rtl:rotate-180" />
                    </a>
                @endif
            </li>

            @foreach ($entries as $entry)
                <li>
                    @if ($entry === '...')
                        <span class="{{ $itemBase }} text-slate-400" aria-hidden="true">…</span>
                    @elseif ($entry === $current)
                        <span aria-current="page" class="{{ $itemBase }} {{ $currentClass }}">{{ $entry }}</span>
                    @else
                        <a href="{{ $paginator->url($entry) }}" aria-label="الصفحة {{ $entry }}" class="{{ $itemBase }} {{ $linkClass }}">{{ $entry }}</a>
                    @endif
                </li>
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ $nextLabel }}" class="{{ $itemBase }} {{ $linkClass }}">
                        <x-ui.icon name="chevron-right" size="size-4" class="rtl:rotate-180" />
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ $nextLabel }}" class="{{ $itemBase }} {{ $disabledClass }}">
                        <x-ui.icon name="chevron-right" size="size-4" class="rtl:rotate-180" />
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
