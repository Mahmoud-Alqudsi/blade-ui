{{--
|--------------------------------------------------------------------------
| ui/tabs and ui/tab-panel — tabs (ARIA Tabs pattern)
|--------------------------------------------------------------------------
| Props:
|   items   : array of [key => label] pairs
|   default : default key (the first key when omitted)
| Keyboard: left/right arrows (automatically reversed in RTL), Home, and End.
| Example:
|   <x-ui.tabs :items="['profile' => 'Profile', 'security' => 'Security']">
|       <x-ui.tab-panel name="profile">…</x-ui.tab-panel>
|       <x-ui.tab-panel name="security">…</x-ui.tab-panel>
|   </x-ui.tabs>
--}}
@props(['items' => [], 'default' => null])

@php
    $keys = array_map('strval', array_keys($items));
    $default = (string) ($default ?? ($keys[0] ?? ''));
    $uid = 'tabs-'.\Illuminate\Support\Str::random(5);
@endphp

<div x-data="{
         active: @js($default),
         keys: @js($keys),
         uid: @js($uid),
         isRtl() { return getComputedStyle(this.$root).direction === 'rtl'; },
         go(index) {
             this.active = this.keys[(index + this.keys.length) % this.keys.length];
             this.$nextTick(() => document.getElementById(this.uid + '-tab-' + this.active)?.focus());
         },
         step(direction) { this.go(this.keys.indexOf(this.active) + direction); },
     }"
     {{ $attributes->class('w-full') }}>
    <div role="tablist" class="flex gap-1 overflow-x-auto border-b border-slate-200 dark:border-slate-800">
        @foreach ($items as $key => $label)
            <button type="button" role="tab" id="{{ $uid }}-tab-{{ $key }}"
                    aria-controls="{{ $uid }}-panel-{{ $key }}"
                    x-bind:aria-selected="active === @js((string) $key) ? 'true' : 'false'"
                    x-bind:tabindex="active === @js((string) $key) ? 0 : -1"
                    x-on:click="active = @js((string) $key)"
                    x-on:keydown.arrow-right.prevent="step(isRtl() ? -1 : 1)"
                    x-on:keydown.arrow-left.prevent="step(isRtl() ? 1 : -1)"
                    x-on:keydown.home.prevent="go(0)"
                    x-on:keydown.end.prevent="go(keys.length - 1)"
                    x-bind:class="active === @js((string) $key)
                        ? 'border-brand-600 text-brand-700 dark:border-brand-400 dark:text-brand-300'
                        : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200'"
                    class="-mb-px whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-medium transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-500">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="pt-4">{{ $slot }}</div>
</div>
