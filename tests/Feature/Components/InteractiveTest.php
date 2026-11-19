<?php

it('renders accordion items wired to the shared state', function () {
    $this->blade('<x-ui.accordion><x-ui.accordion-item title="سؤال" open>جواب</x-ui.accordion-item></x-ui.accordion>')
        ->assertSee('سؤال')
        ->assertSee('جواب')
        ->assertSee('role="region"', false)
        ->assertSee('x-init="toggle(id)"', false)
        ->assertSee('aria-controls="acc-', false);
});

it('renders a drawer with dialog semantics and named events', function () {
    $this->blade('<x-ui.drawer name="filters" title="تصفية">محتوى<x-slot:footer>تطبيق</x-slot:footer></x-ui.drawer>')
        ->assertSee('role="dialog"', false)
        ->assertSee('aria-labelledby="drawer-filters-title"', false)
        ->assertSee('x-on:open-drawer-filters.window', false)
        ->assertSee('ltr:translate-x-full', false)
        ->assertSee('تطبيق');
});

it('places a start-side drawer on the logical start edge', function () {
    $this->blade('<x-ui.drawer name="nav" side="start">محتوى</x-ui.drawer>')
        ->assertSee('start-0', false)
        ->assertSee('ltr:-translate-x-full', false);
});

it('renders the password field with a visibility toggle', function () {
    $this->blade('<x-forms.password name="password" />')
        ->assertSee('type="password"', false)
        ->assertSee('id="input-password"', false)
        ->assertSee('x-bind:aria-pressed', false);
});

it('renders a file input with validation wiring', function () {
    $this->withViewErrors(['avatar' => 'الملف كبير جدًا'])
        ->blade('<x-forms.file name="avatar" label="الصورة" accept="image/*" />')
        ->assertSee('type="file"', false)
        ->assertSee('accept="image/*"', false)
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('الملف كبير جدًا');
});

it('does not depend on Alpine for non-dismissible alerts', function () {
    $this->blade('<x-ui.alert>نص</x-ui.alert>')
        ->assertDontSee('x-show', false)
        ->assertDontSee('x-cloak', false);
});

it('casts numeric tab keys to strings', function () {
    $this->blade('<x-ui.tabs :items="[1 => \'أول\', 2 => \'ثاني\']"><x-ui.tab-panel name="1">a</x-ui.tab-panel></x-ui.tabs>')
        ->assertSee("active: '1'", false);
});
