{{--
|--------------------------------------------------------------------------
| forms/date-picker — date picker
|--------------------------------------------------------------------------
| The calendar uses Intl to display month and day names in the page locale, and submits the
| date in ISO format (YYYY-MM-DD) through a hidden input named name.
| Props:
|   name, id, value (YYYY-MM-DD), label, hint, required, disabled, wrapperClass
|   min, max       : date limits in YYYY-MM-DD format
|   placeholder    : button text when no date is selected
|   weekStart      : first day of the week (0 Sunday … 6 Saturday); defaults to 0
|   locale         : display locale (defaults to the application locale)
|   todayLabel, clearLabel : translatable labels
| Additional attributes (such as wire:model) are passed to the hidden input; input and change events fire on selection.
| Example: <x-forms.date-picker name="birth_date" label="Date of birth" max="2026-12-31" :week-start="6" />
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'label' => null,
    'hint' => null,
    'min' => null,
    'max' => null,
    'placeholder' => 'اختر التاريخ',
    'weekStart' => 0,
    'locale' => null,
    'todayLabel' => 'اليوم',
    'clearLabel' => 'مسح',
    'prevLabel' => 'الشهر السابق',
    'nextLabel' => 'الشهر التالي',
    'required' => false,
    'disabled' => false,
    'wrapperClass' => null,
])

@php
    $errors ??= new \Illuminate\Support\ViewErrorBag;

    $key = $name ? trim(str_replace(['[', ']'], ['.', ''], $name), '.') : null;
    $id ??= $key ? 'date-'.str_replace('.', '-', $key) : 'date-'.\Illuminate\Support\Str::random(6);

    $hasError = $key && $errors->has($key);
    $current = (string) ($key ? old($key, $value) : $value);
    $locale = str_replace('_', '-', $locale ?: app()->getLocale());

    $describedBy = collect([$hasError ? "{$id}-error" : null, ($hint && ! $hasError) ? "{$id}-hint" : null])
        ->filter()->implode(' ');

    $triggerClass = [
        'flex w-full items-center justify-between gap-2 rounded-lg border bg-white py-2.5 ps-3.5 pe-3 text-start text-sm shadow-xs transition-colors',
        'focus-visible:outline-hidden focus-visible:ring-2',
        'disabled:cursor-not-allowed disabled:bg-slate-50 disabled:opacity-60',
        'dark:bg-slate-900 dark:disabled:bg-slate-800/60',
        $hasError
            ? 'border-red-500 focus-visible:ring-red-500/40 dark:border-red-400'
            : 'border-slate-300 focus-visible:border-brand-500 focus-visible:ring-brand-500/40 dark:border-slate-700 dark:focus-visible:border-brand-400',
    ];

    $navButton = 'inline-flex size-8 items-center justify-center rounded-lg text-slate-600 transition-colors hover:bg-slate-100 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-slate-300 dark:hover:bg-slate-800';
@endphp

<div @class(['w-full', $wrapperClass])>
    @if ($label)
        <x-forms.label :for="$id" :required="$required" class="mb-1.5">{{ $label }}</x-forms.label>
    @endif

    <div class="relative"
         x-data="{
             open: false,
             value: @js($current),
             min: @js($min),
             max: @js($max),
             locale: @js($locale),
             weekStart: @js((int) $weekStart),
             viewYear: 0,
             viewMonth: 0,
             todayIso: '',
             init() {
                 const base = this.parse(this.value) ?? new Date();
                 this.viewYear = base.getFullYear();
                 this.viewMonth = base.getMonth();
                 this.todayIso = this.iso(new Date());
                 this.$watch('value', () => this.$nextTick(() => {
                     this.$refs.field.dispatchEvent(new Event('input', { bubbles: true }));
                     this.$refs.field.dispatchEvent(new Event('change', { bubbles: true }));
                 }));
             },
             parse(text) {
                 if (!text) return null;
                 const [year, month, day] = text.split('-').map(Number);
                 return new Date(year, month - 1, day);
             },
             iso(date) {
                 const pad = number => String(number).padStart(2, '0');
                 return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate());
             },
             format(text, style) {
                 return new Intl.DateTimeFormat(this.locale, { dateStyle: style }).format(this.parse(text));
             },
             get displayLabel() {
                 return this.value ? this.format(this.value, 'long') : '';
             },
             get monthLabel() {
                 return new Intl.DateTimeFormat(this.locale, { month: 'long', year: 'numeric' }).format(new Date(this.viewYear, this.viewMonth, 1));
             },
             get weekdays() {
                 return Array.from({ length: 7 }, (_, index) =>
                     new Intl.DateTimeFormat(this.locale, { weekday: 'short' }).format(new Date(2023, 0, 1 + ((index + this.weekStart) % 7))));
             },
             get days() {
                 const first = new Date(this.viewYear, this.viewMonth, 1);
                 const offset = (first.getDay() - this.weekStart + 7) % 7;
                 const total = new Date(this.viewYear, this.viewMonth + 1, 0).getDate();
                 const cells = [];
                 for (let index = 0; index < offset; index++) cells.push(null);
                 for (let day = 1; day <= total; day++) {
                     const date = new Date(this.viewYear, this.viewMonth, day);
                     cells.push({ day: day, iso: this.iso(date), disabled: this.isDisabled(date) });
                 }
                 return cells;
             },
             isDisabled(date) {
                 const text = this.iso(date);
                 return Boolean((this.min && text < this.min) || (this.max && text > this.max));
             },
             shift(step) {
                 const date = new Date(this.viewYear, this.viewMonth + step, 1);
                 this.viewYear = date.getFullYear();
                 this.viewMonth = date.getMonth();
             },
             pick(cell) {
                 if (!cell || cell.disabled) return;
                 this.value = cell.iso;
                 const date = this.parse(cell.iso);
                 this.viewYear = date.getFullYear();
                 this.viewMonth = date.getMonth();
                 this.open = false;
                 this.$refs.trigger.focus();
             },
             pickToday() {
                 const today = new Date();
                 this.pick({ iso: this.iso(today), disabled: this.isDisabled(today) });
             },
             clear() {
                 this.value = '';
                 this.open = false;
                 this.$refs.trigger.focus();
             },
         }"
         x-on:click.outside="open = false"
         x-on:keydown.escape="if (open) { open = false; $refs.trigger.focus(); }">
        <input type="hidden" x-ref="field" name="{{ $name }}" value="{{ $current }}" x-bind:value="value" {{ $attributes }}>

        <button type="button" x-ref="trigger" id="{{ $id }}" aria-haspopup="dialog"
                x-bind:aria-expanded="open ? 'true' : 'false'" aria-controls="{{ $id }}-calendar"
                x-on:click="open = !open"
                @if ($disabled) disabled @endif
                @if ($hasError) aria-invalid="true" @endif
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
                @class($triggerClass)>
            <span x-text="displayLabel || @js($placeholder)"
                  x-bind:class="displayLabel ? 'text-slate-900 dark:text-slate-100' : 'text-slate-400 dark:text-slate-500'"
                  class="truncate"></span>
            <x-ui.icon name="calendar" size="size-5" class="text-slate-400 dark:text-slate-500" />
        </button>

        <div x-show="open" x-cloak id="{{ $id }}-calendar" role="dialog" aria-label="{{ $label ?? $placeholder }}"
             x-transition:enter="transition ease-out duration-100 motion-reduce:transition-none"
             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="absolute start-0 z-40 mt-2 w-72 origin-top rounded-xl border border-slate-200 bg-white p-3 shadow-lg dark:border-slate-700 dark:bg-slate-900">
            <div class="mb-2 flex items-center justify-between gap-2">
                <button type="button" x-on:click="shift(-1)" aria-label="{{ $prevLabel }}" class="{{ $navButton }}">
                    <x-ui.icon name="chevron-left" size="size-4" class="rtl:rotate-180" />
                </button>
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100" aria-live="polite" x-text="monthLabel"></p>
                <button type="button" x-on:click="shift(1)" aria-label="{{ $nextLabel }}" class="{{ $navButton }}">
                    <x-ui.icon name="chevron-right" size="size-4" class="rtl:rotate-180" />
                </button>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-slate-500 dark:text-slate-400" aria-hidden="true">
                <template x-for="weekday in weekdays" x-bind:key="weekday">
                    <span class="py-1" x-text="weekday"></span>
                </template>
            </div>

            <div class="mt-1 grid grid-cols-7 gap-1">
                <template x-for="(cell, index) in days" x-bind:key="viewYear + '-' + viewMonth + '-' + index">
                    <div class="flex size-9 items-center justify-center">
                        <button type="button" x-show="cell" x-text="cell ? cell.day : ''"
                                x-on:click="pick(cell)"
                                x-bind:disabled="cell ? cell.disabled : true"
                                x-bind:aria-label="cell ? format(cell.iso, 'full') : null"
                                x-bind:aria-pressed="cell && cell.iso === value ? 'true' : 'false'"
                                x-bind:class="cell && cell.iso === value
                                    ? 'bg-brand-600 text-white hover:bg-brand-700'
                                    : (cell && cell.iso === todayIso
                                        ? 'text-brand-700 ring-1 ring-brand-500 hover:bg-brand-50 dark:text-brand-300 dark:hover:bg-brand-500/10'
                                        : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800')"
                                class="size-9 rounded-lg text-sm transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:bg-transparent"></button>
                    </div>
                </template>
            </div>

            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3 text-sm dark:border-slate-800">
                <button type="button" x-on:click="pickToday()"
                        class="rounded-md px-2 py-1 font-medium text-brand-600 hover:bg-brand-50 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-brand-300 dark:hover:bg-brand-500/10">{{ $todayLabel }}</button>
                @unless ($required)
                    <button type="button" x-on:click="clear()"
                            class="rounded-md px-2 py-1 text-slate-500 hover:bg-slate-100 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-slate-400 dark:hover:bg-slate-800">{{ $clearLabel }}</button>
                @endunless
            </div>
        </div>
    </div>

    @if ($key)
        @error($key)
            <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    @endif

    @if ($hint && ! $hasError)
        <p id="{{ $id }}-hint" class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>
