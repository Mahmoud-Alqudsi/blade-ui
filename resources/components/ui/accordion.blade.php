{{--
|--------------------------------------------------------------------------
| ui/accordion and ui/accordion-item — accordion
|--------------------------------------------------------------------------
| Props (accordion): multiple (allow multiple items to stay open; false by default)
| Props (accordion-item): title, open (open initially)
| Example:
|   <x-ui.accordion>
|       <x-ui.accordion-item title="How do I sign up?" open>…</x-ui.accordion-item>
|       <x-ui.accordion-item title="What payment methods are available?">…</x-ui.accordion-item>
|   </x-ui.accordion>
--}}
@props(['multiple' => false])

<div x-data="{
         openIds: [],
         multiple: @js($multiple),
         isOpen(id) { return this.openIds.includes(id); },
         toggle(id) {
             if (this.isOpen(id)) {
                 this.openIds = this.openIds.filter(item => item !== id);
             } else {
                 this.openIds = this.multiple ? [...this.openIds, id] : [id];
             }
         },
     }"
     {{ $attributes->class('divide-y divide-slate-200 overflow-hidden rounded-2xl border border-slate-200 bg-white dark:divide-slate-800 dark:border-slate-800 dark:bg-slate-900') }}>
    {{ $slot }}
</div>
