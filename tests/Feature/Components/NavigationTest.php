<?php

use Illuminate\Pagination\LengthAwarePaginator;

it('renders a breadcrumb with the current page marked', function () {
    $this->blade('<x-ui.breadcrumb :items="[[\'label\' => \'الرئيسية\', \'href\' => \'/\'], [\'label\' => \'المستخدمون\']]" />')
        ->assertSee('aria-label="مسار التنقل"', false)
        ->assertSee('href="/"', false)
        ->assertSee('aria-current="page"', false)
        ->assertSee('المستخدمون');
});

it('renders pagination with the current page and an ellipsis', function () {
    $paginator = new LengthAwarePaginator(collect(range(1, 10)), 200, 10, 10, ['path' => '/users']);

    $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => $paginator])
        ->assertSee('aria-current="page"', false)
        ->assertSee('rel="prev"', false)
        ->assertSee('rel="next"', false)
        ->assertSee('…')
        ->assertSee('عرض 91 إلى 100 من 200');
});

it('disables the previous link on the first page', function () {
    $paginator = new LengthAwarePaginator(collect(range(1, 10)), 50, 10, 1, ['path' => '/users']);

    $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => $paginator])
        ->assertDontSee('rel="prev"', false)
        ->assertSee('aria-disabled="true"', false);
});

it('renders nothing when there is a single page', function () {
    $paginator = new LengthAwarePaginator(collect(range(1, 3)), 3, 10, 1, ['path' => '/users']);

    $this->blade('<x-ui.pagination :paginator="$paginator" />', ['paginator' => $paginator])
        ->assertDontSee('role="navigation"', false);
});
