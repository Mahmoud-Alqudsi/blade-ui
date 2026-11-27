{{--
|--------------------------------------------------------------------------
| forms/select — form select
|--------------------------------------------------------------------------
| Props:
|   name, id, value, label, hint, required, disabled, wrapperClass
|   options     : array of [value => label] pairs (or pass <option> elements through the slot)
|   placeholder : optional empty first option
| Example: <x-forms.select name="role" label="Role" :options="['admin' => 'Admin', 'user' => 'User']" placeholder="Choose a role" />
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'label' => null,
    'hint' => null,
    'options' => [],
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'wrapperClass' => null,
])

@php
    $errors ??= new \Illuminate\Support\ViewErrorBag;

    $key = $name ? trim(str_replace(['[', ']'], ['.', ''], $name), '.') : null;
    $id ??= $key ? 'select-'.str_replace('.', '-', $key) : 'select-'.\Illuminate\Support\Str::random(6);

    $hasError = $key && $errors->has($key);
    $current = (string) ($key ? old($key, $value) : $value);

    $describedBy = collect([$hasError ? "{$id}-error" : null, ($hint && ! $hasError) ? "{$id}-hint" : null])
        ->filter()->implode(' ');

    $fieldClass = [
        'block w-full appearance-none rounded-lg border bg-white py-2.5 ps-3.5 pe-10 text-sm text-slate-900 shadow-xs transition-colors',
        'focus-visible:outline-hidden focus-visible:ring-2',
        'disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-500',
        'dark:bg-slate-900 dark:text-slate-100 dark:disabled:bg-slate-800/60',
        $hasError
            ? 'border-red-500 focus-visible:ring-red-500/40 dark:border-red-400'
            : 'border-slate-300 focus-visible:border-brand-500 focus-visible:ring-brand-500/40 dark:border-slate-700 dark:focus-visible:border-brand-400',
    ];
@endphp

<div @class(['w-full', $wrapperClass])>
    @if ($label)
        <x-blade-ui::forms.label :for="$id" :required="$required" class="mb-1.5">{{ $label }}</x-blade-ui::forms.label>
    @endif

    <div class="relative">
        <select {{ $attributes->merge([
            'id' => $id,
            'name' => $name,
            'required' => $required,
            'disabled' => $disabled,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-describedby' => $describedBy ?: null,
        ])->twClass($fieldClass) }}>
            @if ($placeholder !== null)
                <option value="" @selected($current === '')>{{ $placeholder }}</option>
            @endif

            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @selected($current === (string) $optionValue)>{{ $optionLabel }}</option>
            @endforeach

            {{ $slot }}
        </select>

        <span class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3 text-slate-500 dark:text-slate-400">
            <x-blade-ui::ui.icon name="chevron-down" size="size-4" />
        </span>
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
