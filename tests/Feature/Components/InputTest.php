<?php

it('renders the label linked to the input', function () {
    $this->blade('<x-forms.input name="email" label="البريد" />')
        ->assertSee('for="input-email"', false)
        ->assertSee('id="input-email"', false)
        ->assertSee('البريد');
});

it('shows validation errors and wires up aria attributes', function () {
    $this->withViewErrors(['email' => 'البريد الإلكتروني غير صالح'])
        ->blade('<x-forms.input name="email" label="البريد" />')
        ->assertSee('البريد الإلكتروني غير صالح')
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('aria-describedby="input-email-error"', false)
        ->assertSee('border-red-500', false);
});

it('renders the hint text when there is no error', function () {
    $this->blade('<x-forms.input name="name" label="الاسم" hint="الاسم الكامل" />')
        ->assertSee('الاسم الكامل')
        ->assertSee('aria-describedby="input-name-hint"', false);
});

it('renders icon slots with logical padding', function () {
    $this->blade('<x-forms.input name="q"><x-slot:icon-left><i data-test="icon"></i></x-slot:icon-left></x-forms.input>')
        ->assertSee('data-test="icon"', false)
        ->assertSee('ps-10', false);
});

it('includes dark mode classes', function () {
    $this->blade('<x-forms.input name="q" />')->assertSee('dark:bg-slate-900', false);
});

it('never prefills password fields', function () {
    $this->blade('<x-forms.input name="password" type="password" value="secret" />')
        ->assertDontSee('value="secret"', false);
});

it('renders select options and marks the selected one', function () {
    $this->blade('<x-forms.select name="role" value="user" :options="[\'admin\' => \'مدير\', \'user\' => \'مستخدم\']" />')
        ->assertSee('مدير')
        ->assertSeeInOrder(['value="user"', 'selected']);
});

it('renders textarea content', function () {
    $this->blade('<x-forms.textarea name="bio" value="نبذة قصيرة" />')
        ->assertSee('>نبذة قصيرة</textarea>', false);
});

it('renders a checkbox as checked when requested', function () {
    $this->blade('<x-forms.checkbox name="remember" label="تذكرني" checked />')
        ->assertSee('type="checkbox"', false)
        ->assertSee('checked="checked"', false)
        ->assertSee('تذكرني');
});

it('renders a switch with the switch role', function () {
    $this->blade('<x-forms.switch name="notify" label="إشعارات" off-value="0" />')
        ->assertSee('role="switch"', false)
        ->assertSee('type="hidden"', false);
});
