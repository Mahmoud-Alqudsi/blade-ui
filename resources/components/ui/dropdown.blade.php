{{--
|--------------------------------------------------------------------------
| ui/dropdown — dropdown menu
|--------------------------------------------------------------------------
| Props:
|   align : start | end (panel alignment; logical and mirrored in RTL)
|   width : Tailwind width class (defaults to w-56)
| Slots: trigger and default content for menu items
| Keyboard: arrow keys navigate; Esc closes and returns focus to the trigger.
| Example:
|   <x-ui.dropdown align="end">
|       <x-slot:trigger><x-ui.button variant="outline">Account</x-ui.button></x-slot:trigger>
|       <x-ui.dropdown-item href="/profile">Profile</x-ui.dropdown-item>
|       <x-ui.dropdown-item danger>Sign out</x-ui.dropdown-item>
|   </x-ui.dropdown>
--}}
@props(['align' => 'start', 'width' => 'w-56'])

@php
    $alignClass = $align === 'end' ? 'end-0' : 'start-0';
@endphp

<div class="relative inline-block"
     x-data="{
         open: false,
         syncTrigger() {
             const trigger = this.$refs.trigger.firstElementChild;
             if (trigger) {
                 trigger.setAttribute('aria-haspopup', 'menu');
                 trigger.setAttribute('aria-expanded', this.open ? 'true' : 'false');
             }
         },
         close() {
             this.open = false;
             this.$refs.trigger.firstElementChild?.focus();
         },
     }"
     x-effect="syncTrigger()"
     x-init="$watch('open', value => value && $nextTick(() => $focus.within($refs.panel).first()))"
     x-on:click.outside="open = false"
     x-on:keydown.escape.window="if (open) close()">
    <div x-ref="trigger" x-on:click="open = !open" x-on:keydown.arrow-down.prevent="open = true">{{ $trigger }}</div>

    <div x-ref="panel" x-show="open" x-cloak role="menu"
         x-transition:enter="transition ease-out duration-100 motion-reduce:transition-none"
         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75 motion-reduce:transition-none"
         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         x-on:click="open = false"
         x-on:keydown.arrow-down.prevent="$focus.within($refs.panel).wrap().next()"
         x-on:keydown.arrow-up.prevent="$focus.within($refs.panel).wrap().previous()"
         x-on:keydown.tab="open = false"
         {{ $attributes->class(['absolute z-40 mt-2 origin-top rounded-xl border border-slate-200 bg-white p-1 shadow-lg dark:border-slate-700 dark:bg-slate-900', $width, $alignClass]) }}>
        {{ $slot }}
    </div>
</div>
