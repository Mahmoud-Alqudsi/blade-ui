{{--
|--------------------------------------------------------------------------
| forms/checkbox
|--------------------------------------------------------------------------
| Props: name, id, value, label (or use the slot), hint, checked, disabled
| The checked state comes from old() after validation failure, or from the checked prop.
| Example: <x-forms.checkbox name="remember" label="Remember me" />
--}}
@props([
    'name' => null,
    'id' => null,
    'value' => '1',
    'label' => null,
    'hint' => null,
    'checked' => false,
    'disabled' => false,
])

@php
    $errors ??= new \Illuminate\Support\ViewErrorBag;

    $key = $name ? trim(str_replace(['[', ']'], ['.', ''], $name), '.') : null;
    $id ??= 'checkbox-'.\Illuminate\Support\Str::slug(((string) $key).' '.((string) $value), '-').'-'.\Illuminate\Support\Str::random(3);

    $hasError = $key && $errors->has($key);

    $old = $key ? old($key) : null;
    $isChecked = ($key && session()->hasOldInput())
        ? (is_array($old) ? in_array((string) $value, array_map('strval', $old), true) : (string) $old === (string) $value)
        : (bool) $checked;

    $describedBy = collect([$hasError ? "{$id}-error" : null, ($hint && ! $hasError) ? "{$id}-hint" : null])
        ->filter()->implode(' ');
@endphp

<div class="w-full">
    <div class="flex items-start gap-3">
        <input {{ $attributes->merge([
            'id' => $id,
            'type' => 'checkbox',
            'name' => $name,
            'value' => $value,
            'checked' => $isChecked,
            'disabled' => $disabled,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-describedby' => $describedBy ?: null,
        ])->class([
            'mt-0.5 size-4 shrink-0 rounded border bg-white text-brand-600 transition-colors',
            'focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900',
            'disabled:cursor-not-allowed disabled:opacity-60',
            'dark:border-slate-600 dark:bg-slate-900',
            $hasError ? 'border-red-500' : 'border-slate-300',
        ]) }}>

        @if ($label || $slot->isNotEmpty())
            <div class="text-sm">
                <label for="{{ $id }}" class="font-medium text-slate-700 dark:text-slate-200">{{ $label ?? $slot }}</label>
                @if ($hint && ! $hasError)
                    <p id="{{ $id }}-hint" class="mt-0.5 text-slate-500 dark:text-slate-400">{{ $hint }}</p>
                @endif
            </div>
        @endif
    </div>

    @if ($key)
        @error($key)
            <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    @endif
</div>
