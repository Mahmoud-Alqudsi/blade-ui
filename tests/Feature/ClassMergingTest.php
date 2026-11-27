<?php

use BladeUi\Support\ClassMerger;
use BladeUi\Support\PassthroughMerger;
use BladeUi\Support\TailwindClassMerger;

$installed = TailwindClassMerger::isInstalled();

it('binds the right merger for the environment', function () use ($installed) {
    expect(app(ClassMerger::class))
        ->toBeInstanceOf($installed ? TailwindClassMerger::class : PassthroughMerger::class);
});

it('sends component classes first and caller classes last to the merger', function () {
    $spy = new class implements ClassMerger
    {
        public array $calls = [];

        public function merge(string ...$classes): string
        {
            $this->calls[] = $classes;

            return 'from-spy';
        }
    };
    app()->instance(ClassMerger::class, $spy);

    $html = (string) $this->blade('<x-ui.badge class="mt-4">x</x-ui.badge>');

    expect($spy->calls)->toHaveCount(1)
        ->and($spy->calls[0][1])->toBe('mt-4')
        ->and($spy->calls[0][0])->toContain('rounded-full')
        ->and(bladeUiClassTokens($html))->toBe(['from-spy']);
});

it('works without caller classes', function () {
    $tokens = bladeUiClassTokens((string) $this->blade('<x-ui.badge>x</x-ui.badge>'));

    expect($tokens)->toContain('rounded-full', 'inline-flex');
});

it('keeps both lists, in order, when nothing is merged', function () {
    app()->instance(ClassMerger::class, new PassthroughMerger);

    $tokens = bladeUiClassTokens((string) $this->blade('<x-ui.button class="mt-4 bg-red-500">x</x-ui.button>'));

    expect($tokens)->toContain('bg-brand-600', 'bg-red-500', 'mt-4')
        ->and(array_search('bg-red-500', $tokens))->toBeGreaterThan(array_search('bg-brand-600', $tokens));
});

it('joins classes without touching attributes other than class', function () {
    app()->instance(ClassMerger::class, new PassthroughMerger);

    $this->blade('<x-ui.button class="mt-4" data-id="7" type="submit">x</x-ui.button>')
        ->assertSee('data-id="7"', false)
        ->assertSee('type="submit"', false);
});

it('lets caller classes beat conflicting component defaults', function () {
    $tokens = bladeUiClassTokens((string) $this->blade('<x-ui.button class="bg-red-500 px-8">x</x-ui.button>'));

    expect($tokens)->toContain('bg-red-500', 'px-8', 'hover:bg-brand-700')
        ->not->toContain('bg-brand-600')
        ->not->toContain('px-4');
})->skip(! $installed, 'Requires stamat/tailwind-merge-php');

it('keeps classes that do not conflict', function () {
    $tokens = bladeUiClassTokens((string) $this->blade('<x-ui.button class="mt-4 shadow-lg">x</x-ui.button>'));

    expect($tokens)->toContain('mt-4', 'shadow-lg', 'rounded-lg', 'inline-flex', 'dark:focus-visible:ring-offset-slate-900');
})->skip(! $installed, 'Requires stamat/tailwind-merge-php');

it('resolves conflicts between the component own default classes', function () {
    $tokens = bladeUiClassTokens((string) $this->blade('<x-ui.button variant="success">x</x-ui.button>'));

    expect($tokens)->toContain('focus-visible:ring-emerald-500')
        ->not->toContain('focus-visible:ring-brand-500');
})->skip(! $installed, 'Requires stamat/tailwind-merge-php');

it('merges v4 utilities on form fields', function () {
    $tokens = bladeUiClassTokens((string) $this->blade('<x-forms.input name="q" class="rounded-full shadow-lg" />'), 'input');

    expect($tokens)->toContain('rounded-full', 'shadow-lg')
        ->not->toContain('rounded-lg')
        ->not->toContain('shadow-xs');
})->skip(! $installed, 'Requires stamat/tailwind-merge-php');

it('decides when to use the Tailwind merger', function () {
    expect(TailwindClassMerger::shouldUse('auto', true))->toBeTrue()
        ->and(TailwindClassMerger::shouldUse('auto', false))->toBeFalse()
        ->and(TailwindClassMerger::shouldUse(null, true))->toBeTrue()
        ->and(TailwindClassMerger::shouldUse(false, true))->toBeFalse()
        ->and(TailwindClassMerger::shouldUse('false', true))->toBeFalse()
        ->and(TailwindClassMerger::shouldUse(true, true))->toBeTrue()
        ->and(TailwindClassMerger::shouldUse('true', true))->toBeTrue();
});

it('fails fast when the merger is required but not installed', function () {
    TailwindClassMerger::shouldUse(true, false);
})->throws(RuntimeException::class, 'stamat/tailwind-merge-php');

it('joins non-empty class lists in the passthrough merger', function () {
    expect((new PassthroughMerger)->merge('a b', '', ' c '))->toBe('a b c')
        ->and((new PassthroughMerger)->merge('', ''))->toBe('');
});
