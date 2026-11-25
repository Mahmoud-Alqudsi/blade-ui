<?php

use Illuminate\Pagination\LengthAwarePaginator;

$root = dirname(__DIR__, 2);

it('uses Arabic defaults when the app locale is Arabic', function () {
    app()->setLocale('ar');

    $this->blade('<x-ui.alert dismissible>x</x-ui.alert>')->assertSee('aria-label="إغلاق"', false);
    $this->blade('<x-ui.spinner />')->assertSee('aria-label="جارٍ التحميل"', false);
});

it('switches the defaults to English with the application locale', function () {
    app()->setLocale('en');

    $this->blade('<x-ui.alert dismissible>x</x-ui.alert>')->assertSee('aria-label="Close"', false);
    $this->blade('<x-ui.modal name="m">x</x-ui.modal>')->assertSee('aria-label="Close"', false);
    $this->blade('<x-ui.drawer name="d">x</x-ui.drawer>')->assertSee('aria-label="Close"', false);
    $this->blade('<x-ui.toast />')->assertSee('aria-label="Close"', false);
    $this->blade('<x-ui.spinner />')->assertSee('aria-label="Loading"', false);
    $this->blade('<x-ui.theme-toggle />')->assertSee('aria-label="Toggle dark mode"', false);
    $this->blade('<x-ui.breadcrumb :items="[[\'label\' => \'Home\']]" />')->assertSee('aria-label="Breadcrumb"', false);
});

it('translates steps, combobox, password and date picker defaults', function () {
    app()->setLocale('en');

    $this->blade('<x-ui.steps :steps="[\'A\', \'B\']" :current="2" />')
        ->assertSee('aria-label="Progress steps"', false)
        ->assertSee('Completed');

    $this->blade('<x-forms.combobox name="c" :options="[\'a\' => \'Alpha\']" />')
        ->assertSee('placeholder="Select or search…"', false)
        ->assertSee('No results found');

    $this->blade('<x-forms.password />')
        ->assertSee('Password')
        ->assertSee('Show password')
        ->assertSee('Hide password');

    $this->blade('<x-forms.date-picker name="d" />')
        ->assertSee('Select a date')
        ->assertSee('Today')
        ->assertSee('Clear')
        ->assertSee('Previous month')
        ->assertSee('Next month');
});

it('translates the pagination summary and links', function () {
    app()->setLocale('en');

    $paginator = new LengthAwarePaginator(collect(range(1, 10)), 200, 10, 10, ['path' => '/users']);

    $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => $paginator])
        ->assertSee('Showing 91 to 100 of 200')
        ->assertSee('aria-label="Pagination navigation"', false)
        ->assertSee('aria-label="Previous"', false)
        ->assertSee('aria-label="Go to page 9"', false);
});

it('lets explicit props override the translated defaults', function () {
    app()->setLocale('en');

    $this->blade('<x-ui.alert dismissible close-label="Dismiss">x</x-ui.alert>')
        ->assertSee('aria-label="Dismiss"', false)
        ->assertDontSee('aria-label="Close"', false);
});

it('falls back to English for unsupported locales', function () {
    app()->setLocale('fr');

    $this->blade('<x-ui.alert dismissible>x</x-ui.alert>')->assertSee('aria-label="Close"', false);
});

it('keeps the Arabic and English translation files in sync', function () use ($root) {
    $ar = require $root.'/lang/ar/messages.php';
    $en = require $root.'/lang/en/messages.php';

    expect(array_keys($ar))->toEqualCanonicalizing(array_keys($en));

    foreach ($en as $key => $english) {
        preg_match_all('/:\w+/', $english, $englishPlaceholders);
        preg_match_all('/:\w+/', $ar[$key], $arabicPlaceholders);

        expect($arabicPlaceholders[0])->toEqualCanonicalizing($englishPlaceholders[0], "placeholders differ for [{$key}]");
    }
});

it('defines every translation key the components use', function () use ($root) {
    $defined = array_keys(require $root.'/lang/en/messages.php');
    $used = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/resources')) as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            preg_match_all('/blade-ui::messages\.([a-z_]+)/', file_get_contents($file->getPathname()), $matches);
            $used = array_merge($used, $matches[1]);
        }
    }

    expect($used)->not->toBeEmpty()
        ->and(array_diff(array_unique($used), $defined))->toBe([]);
});

it('keeps hard-coded Arabic out of component props', function () use ($root) {
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/resources/components')) as $file) {
        if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
            continue;
        }

        $source = preg_replace('/\{\{--.*?--\}\}/s', '', file_get_contents($file->getPathname()));

        if (preg_match('/@props\(\[.*?\]\)/s', $source, $props)) {
            expect(preg_match('/[\x{0600}-\x{06FF}]/u', $props[0]))->toBe(0, "Arabic default found in {$file->getFilename()}");
        }
    }
});

it('publishes the translations under a dedicated tag', function () {
    $paths = \Illuminate\Support\ServiceProvider::pathsToPublish(\BladeUi\BladeUiServiceProvider::class, 'blade-ui-lang');

    expect($paths)->not->toBeEmpty();
});
