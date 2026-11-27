<?php

it('gives a titleless modal an accessible name', function () {
    $this->blade('<x-ui.modal name="confirm">x</x-ui.modal>')
        ->assertSee('aria-label="confirm"', false)
        ->assertDontSee('aria-labelledby', false);

    $this->blade('<x-ui.modal name="confirm" label="تأكيد الحذف">x</x-ui.modal>')
        ->assertSee('aria-label="تأكيد الحذف"', false);
});

it('gives a titleless drawer an accessible name', function () {
    $this->blade('<x-ui.drawer name="filters">x</x-ui.drawer>')
        ->assertSee('aria-label="filters"', false)
        ->assertDontSee('aria-labelledby', false);
});

it('prefers aria-labelledby when a title exists', function () {
    $this->blade('<x-ui.modal name="m" title="عنوان">x</x-ui.modal>')
        ->assertSee('aria-labelledby="modal-m-title"', false)
        ->assertDontSee('aria-label="m"', false);
});

it('always names the progress bar', function () {
    $this->blade('<x-ui.progress :value="10" />')->assertSee('aria-label="التقدم"', false);

    $this->blade('<x-ui.progress :value="10" label="الملف" />')
        ->assertSee('aria-labelledby="progress-', false)
        ->assertDontSee('aria-label="التقدم"', false);
});

it('hides an unnamed avatar from assistive technology', function () {
    $this->blade('<x-ui.avatar />')
        ->assertSee('aria-hidden="true"', false)
        ->assertDontSee('role="img"', false);

    $this->blade('<x-ui.avatar name="سارة" />')
        ->assertSee('role="img"', false)
        ->assertSee('aria-label="سارة"', false);
});

it('renders disabled pagination controls as links with aria-disabled', function () {
    $paginator = new \Illuminate\Pagination\LengthAwarePaginator(collect(range(1, 10)), 50, 10, 1, ['path' => '/users']);

    $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => $paginator])
        ->assertSee('role="link" aria-disabled="true"', false)
        ->assertDontSee('<span aria-disabled', false);
});

it('keeps the empty-results message outside the combobox listbox', function () {
    $html = (string) $this->blade('<x-forms.combobox name="c" :options="[\'a\' => \'Alpha\']" />');

    expect($html)->toContain('role="status"')
        ->and($html)->toContain('role="listbox"');

    // No roleless <li> inside the listbox (would violate aria-required-children).
    expect(preg_match('/role="listbox"[^>]*>(?:(?!<\/ul>).)*<li(?![^>]*role="option")/s', $html))->toBe(0);
});

it('uses color pairs that meet WCAG contrast for success actions', function () {
    $this->blade('<x-ui.button variant="success">حفظ</x-ui.button>')
        ->assertSee('bg-emerald-700', false)
        ->assertDontSee('bg-emerald-600', false);

    $this->blade('<x-ui.progress :value="50" variant="success" />')
        ->assertSee('bg-emerald-700', false)
        ->assertSee('dark:bg-emerald-500', false);
});

it('uses readable placeholder and icon colors in form fields', function () {
    $this->blade('<x-forms.input name="q" placeholder="بحث" />')
        ->assertSee('placeholder:text-slate-500', false)
        ->assertSee('dark:placeholder:text-slate-400', false)
        ->assertDontSee('dark:placeholder:text-slate-500', false);
});
