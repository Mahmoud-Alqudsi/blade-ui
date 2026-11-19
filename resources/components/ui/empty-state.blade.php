{{--
|--------------------------------------------------------------------------
| ui/empty-state — empty state
|--------------------------------------------------------------------------
| Props: icon (defaults to inbox), title, description
| Default slot: suggested action, such as a button
| Example:
|   <x-ui.empty-state title="No projects yet" description="Create your first project to get started.">
|       <x-ui.button size="sm">New project</x-ui.button>
|   </x-ui.empty-state>
--}}
@props(['icon' => 'inbox', 'title', 'description' => null])

<div {{ $attributes->class('flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-slate-300 p-10 text-center dark:border-slate-700') }}>
    <span class="inline-flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
        <x-ui.icon :name="$icon" size="size-6" />
    </span>

    <h3 class="font-semibold text-slate-900 dark:text-slate-100">{{ $title }}</h3>

    @if ($description)
        <p class="max-w-sm text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-2">{{ $slot }}</div>
    @endif
</div>
