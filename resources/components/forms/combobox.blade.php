{{--
|--------------------------------------------------------------------------
| forms/combobox — searchable select (ARIA Combobox pattern)
|--------------------------------------------------------------------------
| Props:
|   name, id, value, label, hint, required, disabled, wrapperClass
|   options           : array of [value => label] pairs
|   placeholder       : text shown when no value is selected
|   emptyText         : message shown when there are no results
| Keyboard: ↓ ↑ navigate, Enter selects, Esc closes, and Tab closes.
| The value is submitted through a hidden input named name; validation errors are shown automatically.
| Example: <x-forms.combobox name="country" label="Country" :options="$countries" placeholder="Search countries" />
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'label' => null,
    'hint' => null,
    'options' => [],
    'placeholder' => null,
    'emptyText' => null,
    'required' => false,
    'disabled' => false,
    'wrapperClass' => null,
])

@php
    $placeholder ??= __('blade-ui::messages.select_or_search');
    $emptyText ??= __('blade-ui::messages.no_results');
@endphp

@php
    $errors ??= new \Illuminate\Support\ViewErrorBag;

    $key = $name ? trim(str_replace(['[', ']'], ['.', ''], $name), '.') : null;
    $id ??= $key ? 'combobox-'.str_replace('.', '-', $key) : 'combobox-'.\Illuminate\Support\Str::random(6);

    $hasError = $key && $errors->has($key);
    $current = (string) ($key ? old($key, $value) : $value);

    $optionList = collect($options)
        ->map(fn ($optionLabel, $optionValue) => ['value' => (string) $optionValue, 'label' => (string) $optionLabel])
        ->values()
        ->all();

    $currentLabel = collect($optionList)->firstWhere('value', $current)['label'] ?? '';

    $describedBy = collect([$hasError ? "{$id}-error" : null, ($hint && ! $hasError) ? "{$id}-hint" : null])
        ->filter()->implode(' ');

    $fieldClass = [
        'block w-full rounded-lg border bg-white py-2.5 ps-3.5 pe-10 text-sm text-slate-900 shadow-xs transition-colors placeholder:text-slate-500',
        'focus-visible:outline-hidden focus-visible:ring-2',
        'disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-500',
        'dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-400 dark:disabled:bg-slate-800/60',
        $hasError
            ? 'border-red-500 focus-visible:ring-red-500/40 dark:border-red-400'
            : 'border-slate-300 focus-visible:border-brand-500 focus-visible:ring-brand-500/40 dark:border-slate-700 dark:focus-visible:border-brand-400',
    ];
@endphp

<div @class(['w-full', $wrapperClass])>
    @if ($label)
        <x-blade-ui::forms.label :for="$id" :required="$required" class="mb-1.5">{{ $label }}</x-blade-ui::forms.label>
    @endif

    <div class="relative"
         x-data="{
             open: false,
             query: '',
             active: -1,
             uid: @js($id),
             options: @js($optionList),
             selected: @js($current),
             get filtered() {
                 const q = this.query.trim().toLowerCase();
                 return q === '' ? this.options : this.options.filter(option => option.label.toLowerCase().includes(q));
             },
             get selectedLabel() {
                 const match = this.options.find(option => option.value === this.selected);
                 return match ? match.label : '';
             },
             openList() {
                 if (this.open) return;
                 this.open = true;
                 this.active = Math.max(0, this.filtered.findIndex(option => option.value === this.selected));
             },
             close() {
                 this.open = false;
                 this.query = '';
                 this.active = -1;
             },
             choose(option) {
                 this.selected = option.value;
                 this.close();
                 this.$refs.input.focus();
             },
             move(step) {
                 if (!this.open) { this.openList(); return; }
                 const total = this.filtered.length;
                 if (total === 0) return;
                 this.active = (this.active + step + total) % total;
                 this.$nextTick(() => document.getElementById(this.uid + '-option-' + this.active)?.scrollIntoView({ block: 'nearest' }));
             },
             confirm() {
                 if (this.open && this.filtered[this.active]) this.choose(this.filtered[this.active]);
             },
         }"
         x-on:click.outside="close()">
        <input type="hidden" name="{{ $name }}" value="{{ $current }}" x-bind:value="selected">

        <input x-ref="input" type="text" role="combobox" autocomplete="off" id="{{ $id }}"
               value="{{ $currentLabel }}" placeholder="{{ $placeholder }}"
               aria-autocomplete="list" aria-controls="{{ $id }}-listbox"
               x-bind:aria-expanded="open ? 'true' : 'false'"
               x-bind:aria-activedescendant="open && active >= 0 ? uid + '-option-' + active : null"
               x-bind:value="open ? query : selectedLabel"
               x-on:click="openList()"
               x-on:input="query = $event.target.value; open = true; active = 0"
               x-on:keydown.arrow-down.prevent="move(1)"
               x-on:keydown.arrow-up.prevent="move(-1)"
               x-on:keydown.enter.prevent="confirm()"
               x-on:keydown.escape="close()"
               x-on:keydown.tab="close()"
               @if ($required) aria-required="true" @endif
               {{ $attributes->merge([
                   'disabled' => $disabled,
                   'aria-invalid' => $hasError ? 'true' : null,
                   'aria-describedby' => $describedBy ?: null,
               ])->twClass($fieldClass) }}>

        <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-slate-500 dark:text-slate-400">
            <x-blade-ui::ui.icon name="chevron-down" size="size-4" />
        </span>

        <div x-show="open" x-cloak
             class="absolute z-40 mt-1 w-full rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900">
            <ul x-show="filtered.length > 0" role="listbox" id="{{ $id }}-listbox"
                class="max-h-60 overflow-auto p-1">
                <template x-for="(option, index) in filtered" x-bind:key="option.value">
                    <li role="option" x-bind:id="uid + '-option-' + index"
                        x-bind:aria-selected="option.value === selected ? 'true' : 'false'"
                        x-on:click="choose(option)" x-on:mouseenter="active = index"
                        x-bind:class="active === index ? 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-300' : 'text-slate-700 dark:text-slate-200'"
                        class="flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 text-sm">
                        <span x-text="option.label"></span>
                        <x-blade-ui::ui.icon name="check" size="size-4" x-show="option.value === selected" />
                    </li>
                </template>
            </ul>

            <p x-show="filtered.length === 0" role="status" class="px-3 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $emptyText }}</p>
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
