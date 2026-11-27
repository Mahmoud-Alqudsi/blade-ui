{{--
|--------------------------------------------------------------------------
| forms/input — input field
|--------------------------------------------------------------------------
| Props:
|   name, id, type, value, label, hint, required, disabled
|   wrapperClass : additional classes for the outer wrapper
| Slots: icon-left, icon-right (logical positions mirror in RTL)
| Automatically renders the label, @error validation message, and hint text.
| Sets aria-invalid and aria-describedby, and forwards other attributes (wire:model, x-model, autocomplete, etc.) to <input>.
| old() values are restored automatically, except for passwords.
| Example:
|   <x-forms.input name="email" type="email" label="Email" hint="We will not share it" required>
|       <x-slot:icon-left><x-ui.icon name="mail" size="size-5" /></x-slot:icon-left>
|   </x-forms.input>
--}}
@props([
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'label' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'wrapperClass' => null,
])

@php
    $errors ??= new \Illuminate\Support\ViewErrorBag;

    // Convert items[0][name] to items.0.name to match Laravel validation error keys.
    $key = $name ? trim(str_replace(['[', ']'], ['.', ''], $name), '.') : null;
    $id ??= $key ? 'input-'.str_replace('.', '-', $key) : 'input-'.\Illuminate\Support\Str::random(6);

    $hasError = $key && $errors->has($key);
    $hasLeft = isset($iconLeft) && $iconLeft->isNotEmpty();
    $hasRight = isset($iconRight) && $iconRight->isNotEmpty();

    $describedBy = collect([$hasError ? "{$id}-error" : null, ($hint && ! $hasError) ? "{$id}-hint" : null])
        ->filter()->implode(' ');

    $currentValue = $type === 'password' ? null : ($key ? old($key, $value) : $value);

    $fieldClass = [
        'block w-full rounded-lg border bg-white py-2.5 text-sm text-slate-900 shadow-xs transition-colors placeholder:text-slate-500',
        'focus-visible:outline-hidden focus-visible:ring-2',
        'disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-500',
        'dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-400 dark:disabled:bg-slate-800/60',
        $hasLeft ? 'ps-10' : 'ps-3.5',
        $hasRight ? 'pe-10' : 'pe-3.5',
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
        @if ($hasLeft)
            <span class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-slate-500 dark:text-slate-400">{{ $iconLeft }}</span>
        @endif

        <input {{ $attributes->merge([
            'id' => $id,
            'name' => $name,
            'type' => $type,
            'value' => $currentValue,
            'required' => $required,
            'disabled' => $disabled,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-describedby' => $describedBy ?: null,
        ])->class($fieldClass) }}>

        @if ($hasRight)
            <span class="absolute inset-y-0 end-0 flex items-center pe-3 text-slate-500 dark:text-slate-400">{{ $iconRight }}</span>
        @endif
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
