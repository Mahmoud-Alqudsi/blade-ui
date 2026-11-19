{{--
|--------------------------------------------------------------------------
| forms/password — password field with show/hide control
|--------------------------------------------------------------------------
| Wraps forms/input and adds a lock icon and visibility toggle. Props: name (defaults to password),
| id, label, hint, required, disabled, wrapperClass, showLabel, hideLabel.
| Other attributes (autocomplete, wire:model, etc.) are forwarded to the input.
| Example: <x-forms.password name="password" autocomplete="current-password" required />
--}}
@props([
    'name' => 'password',
    'id' => null,
    'label' => 'كلمة المرور',
    'hint' => null,
    'required' => false,
    'disabled' => false,
    'wrapperClass' => null,
    'showLabel' => 'إظهار كلمة المرور',
    'hideLabel' => 'إخفاء كلمة المرور',
])

<div x-data="{ reveal: false }" class="w-full">
    <x-forms.input :name="$name" :id="$id" type="password" :label="$label" :hint="$hint"
                   :required="$required" :disabled="$disabled" :wrapper-class="$wrapperClass"
                   x-bind:type="reveal ? 'text' : 'password'" {{ $attributes }}>
        <x-slot:icon-left><x-ui.icon name="lock" /></x-slot:icon-left>
        <x-slot:icon-right>
            <button type="button" x-on:click="reveal = !reveal"
                    x-bind:aria-label="reveal ? @js($hideLabel) : @js($showLabel)"
                    x-bind:aria-pressed="reveal ? 'true' : 'false'"
                    class="rounded-md p-1 transition-colors hover:text-slate-600 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:hover:text-slate-300">
                <x-ui.icon name="eye" x-show="!reveal" />
                <x-ui.icon name="eye-slash" x-show="reveal" x-cloak />
            </button>
        </x-slot:icon-right>
    </x-forms.input>
</div>
