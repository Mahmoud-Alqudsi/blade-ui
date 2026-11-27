{{--
|--------------------------------------------------------------------------
| forms/switch — toggle switch
|--------------------------------------------------------------------------
| A native checkbox with role="switch", compatible with forms, Livewire, and old().
| Props: name, id, value, label, hint, checked, disabled, offValue
|   offValue : when set, a hidden input submits this value while the switch is off (for example, 0)
| Example: <x-forms.switch name="notifications" label="Email notifications" off-value="0" checked />
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => '1',
    'label' => null,
    'hint' => null,
    'checked' => false,
    'disabled' => false,
    'offValue' => null,
])

@php
    $errors ??= new \Illuminate\Support\ViewErrorBag;

    $key = $name ? trim(str_replace(['[', ']'], ['.', ''], $name), '.') : null;
    $id ??= $key ? 'switch-'.str_replace('.', '-', $key) : 'switch-'.\Illuminate\Support\Str::random(6);

    $hasError = $key && $errors->has($key);

    $old = $key ? old($key) : null;
    $isChecked = ($key && session()->hasOldInput())
        ? (string) $old === (string) $value
        : (bool) $checked;

    $describedBy = collect([$hasError ? "{$id}-error" : null, ($hint && ! $hasError) ? "{$id}-hint" : null])
        ->filter()->implode(' ');
@endphp

<div class="w-full">
    @if ($name && $offValue !== null)
        <input type="hidden" name="{{ $name }}" value="{{ $offValue }}">
    @endif

    <label for="{{ $id }}" @class(['inline-flex items-start gap-3', 'cursor-pointer' => ! $disabled, 'cursor-not-allowed opacity-60' => $disabled])>
        <input {{ $attributes->merge([
            'id' => $id,
            'type' => 'checkbox',
            'role' => 'switch',
            'name' => $name,
            'value' => $value,
            'checked' => $isChecked,
            'disabled' => $disabled,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-describedby' => $describedBy ?: null,
        ])->twClass('peer sr-only') }}>

        <span aria-hidden="true"
              class="relative mt-0.5 h-6 w-11 shrink-0 rounded-full bg-slate-300 transition-colors duration-200 motion-reduce:transition-none dark:bg-slate-700
                     after:absolute after:start-0.5 after:top-0.5 after:size-5 after:rounded-full after:bg-white after:shadow-sm after:transition-transform after:duration-200 motion-reduce:after:transition-none
                     peer-checked:bg-brand-600 peer-checked:after:translate-x-5 rtl:peer-checked:after:-translate-x-5
                     peer-focus-visible:ring-2 peer-focus-visible:ring-brand-500 peer-focus-visible:ring-offset-2 dark:peer-focus-visible:ring-offset-slate-900"></span>

        @if ($label || $slot->isNotEmpty())
            <span class="text-sm">
                <span class="block font-medium text-slate-700 dark:text-slate-200">{{ $label ?? $slot }}</span>
                @if ($hint && ! $hasError)
                    <span id="{{ $id }}-hint" class="mt-0.5 block text-slate-500 dark:text-slate-400">{{ $hint }}</span>
                @endif
            </span>
        @endif
    </label>

    @if ($key)
        @error($key)
            <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    @endif
</div>
