<?php

it('renders a table with caption, head and body', function () {
    $this->blade('<x-ui.table caption="مستخدمون" striped><x-slot:head><tr><th>الاسم</th></tr></x-slot:head><tr><td>سارة</td></tr></x-ui.table>')
        ->assertSee('<caption class="sr-only">مستخدمون</caption>', false)
        ->assertSee('<thead', false)
        ->assertSeeInOrder(['الاسم', 'سارة'])
        ->assertSee('nth-child(even)', false);
});

it('omits thead when no head slot is given', function () {
    $this->blade('<x-ui.table><tr><td>x</td></tr></x-ui.table>')->assertDontSee('<thead', false);
});

it('computes the progress percentage and exposes ARIA values', function () {
    $this->blade('<x-ui.progress :value="30" :max="60" label="التقدم" show-value />')
        ->assertSee('role="progressbar"', false)
        ->assertSee('aria-valuenow="30"', false)
        ->assertSee('aria-valuemax="60"', false)
        ->assertSee('width: 50%', false)
        ->assertSee('50%');
});

it('omits aria-valuenow for an indeterminate progress bar', function () {
    $this->blade('<x-ui.progress indeterminate />')->assertDontSee('aria-valuenow', false);
});

it('renders skeleton lines and hides them from assistive tech', function () {
    $this->blade('<x-ui.skeleton shape="text" :lines="3" />')
        ->assertSee('aria-hidden="true"', false)
        ->assertSee('w-3/4', false);
});

it('renders an empty state with its action', function () {
    $this->blade('<x-ui.empty-state title="لا توجد بيانات" description="ابدأ بإضافة عنصر"><button>إضافة</button></x-ui.empty-state>')
        ->assertSee('لا توجد بيانات')
        ->assertSee('ابدأ بإضافة عنصر')
        ->assertSee('<button>إضافة</button>', false);
});
