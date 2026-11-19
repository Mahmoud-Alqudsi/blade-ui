<?php

it('renders initials for Arabic names', function () {
    $this->blade('<x-ui.avatar name="سارة أحمد" />')->assertSee('سأ');
});

it('renders a status dot on the avatar', function () {
    $this->blade('<x-ui.avatar name="خالد" status="online" />')->assertSee('bg-emerald-500', false);
});

it('renders badge variants', function () {
    $this->blade('<x-ui.badge variant="success" dot>مفعّل</x-ui.badge>')
        ->assertSee('bg-emerald-50', false)
        ->assertSee('مفعّل');
});

it('renders tabs with ARIA roles', function () {
    $this->blade('<x-ui.tabs :items="[\'a\' => \'أ\', \'b\' => \'ب\']"><x-ui.tab-panel name="a">1</x-ui.tab-panel></x-ui.tabs>')
        ->assertSee('role="tablist"', false)
        ->assertSee('role="tab"', false)
        ->assertSee('role="tabpanel"', false);
});

it('links the tooltip to its trigger', function () {
    $this->blade('<x-ui.tooltip text="نسخ">زر</x-ui.tooltip>')
        ->assertSee('role="tooltip"', false)
        ->assertSee('aria-describedby="tip-', false);
});

it('renders the toast container as a live region', function () {
    $this->blade('<x-ui.toast />')->assertSee('aria-live="polite"', false);
});

it('renders the dropdown menu with keyboard support', function () {
    $this->blade('<x-ui.dropdown><x-slot:trigger><button>فتح</button></x-slot:trigger><x-ui.dropdown-item>عنصر</x-ui.dropdown-item></x-ui.dropdown>')
        ->assertSee('role="menu"', false)
        ->assertSee('role="menuitem"', false)
        ->assertSee('x-on:keydown.arrow-down.prevent', false);
});

it('uses only logical (RTL-safe) spacing classes in the form components', function () {
    $html = (string) $this->blade('<x-forms.input name="q"><x-slot:icon-left>i</x-slot:icon-left></x-forms.input>');

    expect($html)->not->toMatch('/\b(pl|pr|ml|mr)-\d/');
});
