{{--
|--------------------------------------------------------------------------
| forms/label — field label
|--------------------------------------------------------------------------
| Props:
|   for      : ID of the associated field
|   required : show an asterisk (hidden from screen readers; also set required on the field)
| Example: <x-forms.label for="email" required>Email</x-forms.label>
--}}
@props(['for' => null, 'required' => false])

<label {{ $attributes->merge(['for' => $for])->twClass('block text-sm font-medium text-slate-700 dark:text-slate-200') }}>
    {{ $slot }}
    @if ($required)
        <span class="ms-0.5 text-red-600 dark:text-red-400" aria-hidden="true">*</span>
    @endif
</label>
