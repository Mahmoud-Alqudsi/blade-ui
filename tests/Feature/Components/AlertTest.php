<?php

it('uses role=alert for danger and role=status for info', function () {
    $this->blade('<x-ui.alert type="danger">خطأ</x-ui.alert>')->assertSee('role="alert"', false);
    $this->blade('<x-ui.alert type="info">معلومة</x-ui.alert>')->assertSee('role="status"', false);
});

it('renders the title and an icon', function () {
    $this->blade('<x-ui.alert type="success" title="تم">نجحت العملية</x-ui.alert>')
        ->assertSee('تم')
        ->assertSee('<svg', false)
        ->assertSee('border-emerald-200', false);
});

it('shows a close button only when dismissible', function () {
    $this->blade('<x-ui.alert dismissible>نص</x-ui.alert>')->assertSee('x-on:click="show = false"', false);
    $this->blade('<x-ui.alert>نص</x-ui.alert>')->assertDontSee('x-on:click="show = false"', false);
});
