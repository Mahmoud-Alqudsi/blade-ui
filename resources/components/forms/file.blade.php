{{--
|--------------------------------------------------------------------------
| forms/file — file upload field
|--------------------------------------------------------------------------
| Props: name, id, label, hint, multiple, required, disabled, wrapperClass
| Other attributes, such as accept, are passed to <input>. Validation errors are shown automatically.
| For multiple files, use name="files[]"; files.* errors are displayed under the files key.
| Set enctype="multipart/form-data" on the form.
| Example: <x-forms.file name="avatar" label="Avatar" accept="image/*" hint="PNG or JPG, up to 2 MB" />
--}}
@props([
    'name' => null,
    'id' => null,
    'label' => null,
    'hint' => null,
    'multiple' => false,
    'required' => false,
    'disabled' => false,
    'wrapperClass' => null,
])

@php
    $errors ??= new \Illuminate\Support\ViewErrorBag;

    $key = $name ? trim(str_replace(['[', ']'], ['.', ''], $name), '.') : null;
    $id ??= $key ? 'file-'.str_replace('.', '-', $key) : 'file-'.\Illuminate\Support\Str::random(6);

    $hasError = $key && $errors->has($key);

    $describedBy = collect([$hasError ? "{$id}-error" : null, ($hint && ! $hasError) ? "{$id}-hint" : null])
        ->filter()->implode(' ');

    $fieldClass = [
        'block w-full cursor-pointer rounded-lg border bg-white text-sm text-slate-700 shadow-xs transition-colors',
        'file:me-3 file:cursor-pointer file:border-0 file:bg-brand-50 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100',
        'focus-visible:outline-hidden focus-visible:ring-2',
        'disabled:cursor-not-allowed disabled:opacity-60',
        'dark:bg-slate-900 dark:text-slate-200 dark:file:bg-brand-500/10 dark:file:text-brand-300 dark:hover:file:bg-brand-500/20',
        $hasError
            ? 'border-red-500 focus-visible:ring-red-500/40 dark:border-red-400'
            : 'border-slate-300 focus-visible:border-brand-500 focus-visible:ring-brand-500/40 dark:border-slate-700 dark:focus-visible:border-brand-400',
    ];
@endphp

<div @class(['w-full', $wrapperClass])>
    @if ($label)
        <x-blade-ui::forms.label :for="$id" :required="$required" class="mb-1.5">{{ $label }}</x-blade-ui::forms.label>
    @endif

    <input {{ $attributes->merge([
        'id' => $id,
        'name' => $name,
        'type' => 'file',
        'multiple' => $multiple,
        'required' => $required,
        'disabled' => $disabled,
        'aria-invalid' => $hasError ? 'true' : null,
        'aria-describedby' => $describedBy ?: null,
    ])->class($fieldClass) }}>

    @if ($key)
        @error($key)
            <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    @endif

    @if ($hint && ! $hasError)
        <p id="{{ $id }}-hint" class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>
