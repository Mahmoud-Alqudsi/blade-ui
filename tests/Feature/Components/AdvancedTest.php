<?php

it('renders a stat card with a colored trend', function () {
    $this->blade('<x-ui.stat-card label="المبيعات" value="500" change="+12%" trend="up" description="عن الشهر الماضي" />')
        ->assertSee('المبيعات')
        ->assertSee('500')
        ->assertSee('+12%')
        ->assertSee('text-emerald-600', false)
        ->assertSee('عن الشهر الماضي');
});

it('uses the down trend color', function () {
    $this->blade('<x-ui.stat-card label="x" value="1" change="-4%" trend="down" />')
        ->assertSee('text-red-600', false);
});

it('marks the current step and labels completed ones', function () {
    $this->blade('<x-ui.steps :steps="[\'الحساب\', \'الملف\', \'التأكيد\']" :current="2" />')
        ->assertSee('aria-current="step"', false)
        ->assertSee('مكتملة')
        ->assertSee('الملف');
});

it('accepts step descriptions', function () {
    $this->blade('<x-ui.steps :steps="[[\'title\' => \'الحساب\', \'description\' => \'بيانات أساسية\']]" :current="1" />')
        ->assertSee('بيانات أساسية');
});

it('renders a combobox with ARIA roles, options and hidden value', function () {
    $this->blade('<x-forms.combobox name="country" label="Country" value="eg" :options="[\'sa\' => \'Saudi\', \'eg\' => \'Egypt\']" />')
        ->assertSee('role="combobox"', false)
        ->assertSee('role="listbox"', false)
        ->assertSee('type="hidden"', false)
        ->assertSee('name="country"', false)
        ->assertSee('Egypt')
        ->assertSee('for="combobox-country"', false)
        ->assertSee('aria-controls="combobox-country-listbox"', false);
});

it('shows combobox validation errors', function () {
    $this->withViewErrors(['country' => 'الدولة مطلوبة'])
        ->blade('<x-forms.combobox name="country" :options="[\'sa\' => \'Saudi\']" />')
        ->assertSee('الدولة مطلوبة')
        ->assertSee('aria-invalid="true"', false);
});

it('renders a date picker with ISO value and dialog semantics', function () {
    $this->blade('<x-forms.date-picker name="due" label="الموعد" value="2026-11-19" min="2026-01-01" />')
        ->assertSee('name="due"', false)
        ->assertSee('2026-11-19')
        ->assertSee('2026-01-01')
        ->assertSee('aria-haspopup="dialog"', false)
        ->assertSee('role="dialog"', false)
        ->assertSee('for="date-due"', false);
});

it('hides the clear button for required date pickers', function () {
    $this->blade('<x-forms.date-picker name="due" required />')->assertDontSee('x-on:click="clear()"', false);
    $this->blade('<x-forms.date-picker name="due" />')->assertSee('x-on:click="clear()"', false);
});

it('does not clip popovers inside cards or modals', function () {
    $this->blade('<x-ui.card>x</x-ui.card>')->assertDontSee('overflow-hidden', false);
    $this->blade('<x-ui.modal name="m">x</x-ui.modal>')->assertDontSee('overflow-hidden', false);
});
