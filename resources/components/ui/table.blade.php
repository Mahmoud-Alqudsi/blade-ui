{{--
|--------------------------------------------------------------------------
| ui/table — table
|--------------------------------------------------------------------------
| Props:
|   striped   : alternate row backgrounds
|   hoverable : highlight rows on hover (enabled by default)
|   dense     : reduce cell padding
|   caption   : caption for screen readers
| Slots: head (<thead> content), foot (<tfoot> content), and default content for <tbody>
| Write regular <tr>, <td>, and <th> elements; cell styles are applied automatically.
| The table scrolls horizontally on small screens.
| Example:
|   <x-ui.table caption="Users" striped>
|       <x-slot:head><tr><th scope="col">Name</th><th scope="col">Status</th></tr></x-slot:head>
|       @foreach ($users as $user) <tr><td>{{ $user->name }}</td><td>…</td></tr> @endforeach
|   </x-ui.table>
--}}
@props(['striped' => false, 'hoverable' => true, 'dense' => false, 'caption' => null])

@php
    $cellPadding = $dense ? '[&_td]:px-4 [&_td]:py-2 [&_th]:px-4 [&_th]:py-2' : '[&_td]:px-4 [&_td]:py-3.5 [&_th]:px-4 [&_th]:py-3';
    $hasHead = isset($head) && $head->isNotEmpty();
    $hasFoot = isset($foot) && $foot->isNotEmpty();
@endphp

<div {{ $attributes->twClass('overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900') }}>
    <table @class(['w-full text-start text-sm text-slate-700 dark:text-slate-200', $cellPadding, '[&_th]:text-start'])>
        @if ($caption)
            <caption class="sr-only">{{ $caption }}</caption>
        @endif

        @if ($hasHead)
            <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold text-slate-600 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-300">
                {{ $head }}
            </thead>
        @endif

        <tbody @class([
            'divide-y divide-slate-200 dark:divide-slate-800',
            '[&>tr:nth-child(even)]:bg-slate-50/70 dark:[&>tr:nth-child(even)]:bg-slate-800/30' => $striped,
            '[&>tr]:transition-colors [&>tr:hover]:bg-slate-50 dark:[&>tr:hover]:bg-slate-800/50' => $hoverable,
        ])>
            {{ $slot }}
        </tbody>

        @if ($hasFoot)
            <tfoot class="border-t border-slate-200 bg-slate-50 font-medium dark:border-slate-800 dark:bg-slate-800/60">
                {{ $foot }}
            </tfoot>
        @endif
    </table>
</div>
