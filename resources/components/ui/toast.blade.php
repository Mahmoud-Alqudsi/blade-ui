{{--
|--------------------------------------------------------------------------
| ui/toast — toast notification container
|--------------------------------------------------------------------------
| Add <x-ui.toast /> once to the main layout, then dispatch notifications from anywhere:
|   $dispatch('toast', { type: 'success', title: 'Saved', message: 'Your details were updated', duration: 5000 })
|   Or from JavaScript: window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'danger', message: '…' } }))
| Types: info | success | warning | danger
| Props: closeLabel
--}}
@props(['closeLabel' => null])

@php
    $closeLabel ??= __('blade-ui::messages.close');
@endphp

@php
    $styles = [
        'info' => 'border-s-sky-500',
        'success' => 'border-s-emerald-500',
        'warning' => 'border-s-amber-500',
        'danger' => 'border-s-red-500',
    ];
@endphp

<div x-data="{
         items: [],
         styles: @js($styles),
         add(detail) {
             const id = Date.now() + Math.random();
             this.items.push({ id, type: detail.type ?? 'info', title: detail.title ?? '', message: detail.message ?? '' });
             setTimeout(() => this.remove(id), detail.duration ?? 5000);
         },
         remove(id) { this.items = this.items.filter(item => item.id !== id); },
     }"
     x-on:toast.window="add($event.detail)"
     aria-live="polite" aria-atomic="false"
     {{ $attributes->class('pointer-events-none fixed bottom-4 end-4 z-[60] flex w-full max-w-sm flex-col gap-2') }}>
    <template x-for="item in items" x-bind:key="item.id">
        <div role="status"
             x-bind:class="styles[item.type] ?? styles.info"
             class="pointer-events-auto flex items-start gap-3 rounded-xl border border-s-4 border-slate-200 bg-white p-4 text-sm shadow-lg dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
            <div class="min-w-0 flex-1">
                <p x-show="item.title" x-text="item.title" class="font-semibold"></p>
                <p x-text="item.message" class="text-slate-600 dark:text-slate-300"></p>
            </div>
            <button type="button" x-on:click="remove(item.id)" aria-label="{{ $closeLabel }}"
                    class="-m-1 rounded-md p-1 text-slate-400 hover:text-slate-700 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:hover:text-slate-200">
                <x-ui.icon name="x-mark" size="size-4" />
            </button>
        </div>
    </template>
</div>
