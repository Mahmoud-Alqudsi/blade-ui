<?php

it('renders a primary medium button by default', function () {
    $this->blade('<x-ui.button>حفظ</x-ui.button>')
        ->assertSee('type="button"', false)
        ->assertSee('bg-brand-600', false)
        ->assertSee('h-10', false)
        ->assertSee('حفظ');
});

it('applies the requested variant and size', function () {
    $this->blade('<x-ui.button variant="danger" size="lg">حذف</x-ui.button>')
        ->assertSee('bg-red-600', false)
        ->assertSee('h-12', false);
});

it('supports the submit type and block width', function () {
    $this->blade('<x-ui.button type="submit" block>إرسال</x-ui.button>')
        ->assertSee('type="submit"', false)
        ->assertSee('w-full', false);
});

it('disables the button and shows a spinner while loading', function () {
    $this->blade('<x-ui.button loading>جارٍ الحفظ</x-ui.button>')
        ->assertSee('disabled="disabled"', false)
        ->assertSee('aria-busy="true"', false)
        ->assertSee('animate-spin', false);
});

it('renders an anchor when href is provided', function () {
    $this->blade('<x-ui.button href="/home">الرئيسية</x-ui.button>')
        ->assertSee('<a ', false)
        ->assertSee('href="/home"', false);
});

it('renders named icon slots', function () {
    $this->blade('<x-ui.button><x-slot:icon-left><i data-test="left"></i></x-slot:icon-left>نص</x-ui.button>')
        ->assertSee('data-test="left"', false);
});

it('merges custom classes and attributes', function () {
    $this->blade('<x-ui.button class="mt-4" data-id="7">نص</x-ui.button>')
        ->assertSee('mt-4', false)
        ->assertSee('data-id="7"', false);
});
