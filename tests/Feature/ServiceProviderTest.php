<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

it('merges the default configuration', function () {
    expect(config('blade-ui.rtl_locales'))->toContain('ar')
        ->and(config('blade-ui.pagination.default'))->toBeFalse()
        ->and(config('blade-ui.vite'))->toBe(['resources/css/app.css', 'resources/js/app.js']);
});

it('resolves package components without publishing anything', function () {
    $this->blade('<x-ui.button>تجربة</x-ui.button>')->assertSee('تجربة');
    $this->blade('<x-forms.input name="q" />')->assertSee('id="input-q"', false);
});

it('loads the package view namespace', function () {
    expect(view()->exists('blade-ui::demo'))->toBeTrue()
        ->and(view()->exists('blade-ui::pagination'))->toBeTrue();
});

it('registers the install command', function () {
    expect(array_keys(Artisan::all()))->toContain('blade-ui:install');
});

it('does not expose the demo page unless it is enabled', function () {
    expect(Route::has('blade-ui.demo'))->toBeFalse();
});
