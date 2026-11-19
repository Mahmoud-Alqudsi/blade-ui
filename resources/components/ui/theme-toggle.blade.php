{{--
|--------------------------------------------------------------------------
| ui/theme-toggle — dark/light mode toggle
|--------------------------------------------------------------------------
| Saves the preference in localStorage and toggles the dark class on <html>.
| Props: label (accessible label)
| Example: <x-ui.theme-toggle />
--}}
@props(['label' => 'تبديل الوضع الداكن'])

<button type="button" aria-label="{{ $label }}"
        x-data="{ dark: document.documentElement.classList.contains('dark') }"
        x-on:click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light')"
        x-bind:aria-pressed="dark ? 'true' : 'false'"
        {{ $attributes->class('inline-flex size-10 items-center justify-center rounded-lg text-slate-600 transition-colors hover:bg-slate-100 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-slate-300 dark:hover:bg-slate-800') }}>
    <x-ui.icon name="moon" x-show="!dark" />
    <x-ui.icon name="sun" x-show="dark" x-cloak />
</button>
