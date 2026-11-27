{{--
|--------------------------------------------------------------------------
| ui/tab-panel — tab content (used inside ui/tabs)
|--------------------------------------------------------------------------
| Props: name (required; must match a tab key)
--}}
@props(['name'])

<div role="tabpanel" tabindex="0" x-cloak
     x-show="active === @js((string) $name)"
     x-bind:id="uid + '-panel-' + @js((string) $name)"
     x-bind:aria-labelledby="uid + '-tab-' + @js((string) $name)"
     {{ $attributes->twClass('text-sm text-slate-600 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-slate-300') }}>
    {{ $slot }}
</div>
