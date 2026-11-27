{{--
|--------------------------------------------------------------------------
| forms/textarea — multiline text field
|--------------------------------------------------------------------------
| Props: name, id, value, label, hint, rows (defaults to 4), required, disabled, wrapperClass
| Example: <x-forms.textarea name="bio" label="Bio" rows="5" hint="Up to 300 characters" />
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => null,
    'label' => null,
    'hint' => null,
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'wrapperClass' => null,
])

@php
    $errors ??= new \Illuminate\Support\ViewErrorBag;

    $key = $name ? trim(str_replace(['[', ']'], ['.', ''], $name), '.') : null;
    $id ??= $key ? 'textarea-'.str_replace('.', '-', $key) : 'textarea-'.\Illuminate\Support\Str::random(6);

    $hasError = $key && $errors->has($key);
    $current = $key ? old($key, $value) : $value;

    $describedBy = collect([$hasError ? "{$id}-error" : null, ($hint && ! $hasError) ? "{$id}-hint" : null])
        ->filter()->implode(' ');

    $fieldClass = [
        'block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-xs transition-colors placeholder:text-slate-500',
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

    <textarea {{ $attributes->merge([
        'id' => $id,
        'name' => $name,
        'rows' => $rows,
        'required' => $required,
        'disabled' => $disabled,
        'aria-invalid' => $hasError ? 'true' : null,
        'aria-describedby' => $describedBy ?: null,
    ])->class($fieldClass) }}>{{ $current ?? (string) $slot }}</textarea>

    @if ($key)
        @error($key)
            <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    @endif

    @if ($hint && ! $hasError)
        <p id="{{ $id }}-hint" class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>
