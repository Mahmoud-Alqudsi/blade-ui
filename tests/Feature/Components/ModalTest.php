<?php

it('exposes dialog semantics and a labelled title', function () {
    $this->blade('<x-ui.modal name="confirm" title="تأكيد">المحتوى</x-ui.modal>')
        ->assertSee('role="dialog"', false)
        ->assertSee('aria-modal="true"', false)
        ->assertSee('aria-labelledby="modal-confirm-title"', false)
        ->assertSee('تأكيد');
});

it('listens for named open and close events', function () {
    $this->blade('<x-ui.modal name="confirm">نص</x-ui.modal>')
        ->assertSee('x-on:open-modal-confirm.window', false)
        ->assertSee('x-on:close-modal-confirm.window', false);
});

it('renders the footer slot', function () {
    $this->blade('<x-ui.modal name="m">نص<x-slot:footer>أزرار</x-slot:footer></x-ui.modal>')
        ->assertSee('أزرار');
});

it('respects the closeable flag', function () {
    $this->blade('<x-ui.modal name="m" :closeable="false">نص</x-ui.modal>')
        ->assertSee('closeable: false', false);
});

it('traps focus and locks scrolling', function () {
    $this->blade('<x-ui.modal name="m">نص</x-ui.modal>')
        ->assertSee('x-trap.noscroll', false);
});
