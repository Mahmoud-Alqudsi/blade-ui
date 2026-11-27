<?php

use BladeUi\Support\TagPrefixer;

it('rewrites opening, closing and self-closing tags', function () {
    $source = '<x-ui.button>a</x-ui.button><x-forms.input name="q" /><x-layouts.guest>x</x-layouts.guest>';

    expect(TagPrefixer::rewrite($source, 'bui'))
        ->toBe('<x-bui::ui.button>a</x-bui::ui.button><x-bui::forms.input name="q" /><x-bui::layouts.guest>x</x-bui::layouts.guest>');
});

it('leaves slots and unrelated components alone', function () {
    $source = '<x-slot:footer>x</x-slot:footer><x-app.card /><x-uikit.button />';

    expect(TagPrefixer::rewrite($source, 'bui'))->toBe($source);
});

it('is a no-op without a prefix', function (mixed $prefix) {
    expect(TagPrefixer::rewrite('<x-ui.button />', $prefix))->toBe('<x-ui.button />');
})->with([null, '', false]);

it('accepts valid prefixes', function (string $prefix) {
    expect(TagPrefixer::normalize($prefix))->toBe($prefix);
})->with(['bui', 'blade-ui', 'My-UI2', 'a']);

it('rejects invalid prefixes', function (mixed $prefix) {
    TagPrefixer::normalize($prefix);
})->with(['1bad', 'bad prefix', 'bad::x', 'a.b', '-bad', true, 12])->throws(InvalidArgumentException::class);
