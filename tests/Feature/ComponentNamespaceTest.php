<?php

it('resolves components through the private internal namespace', function () {
    $this->blade('<x-blade-ui::ui.icon name="mail" />')->assertSee('<svg', false);
    $this->blade('<x-blade-ui::forms.label for="x">Label</x-blade-ui::forms.label>')
        ->assertSee('for="x"', false)
        ->assertSee('Label');
});

it('is not shadowed by project components that reuse the internal names', function () {
    $dir = sys_get_temp_dir().'/bui-shadow-'.uniqid();
    mkdir($dir.'/components/ui', 0777, true);
    mkdir($dir.'/components/forms', 0777, true);
    file_put_contents($dir.'/components/ui/icon.blade.php', 'APP-ICON');
    file_put_contents($dir.'/components/forms/label.blade.php', 'APP-LABEL');

    view()->getFinder()->prependLocation($dir);

    $alert = (string) $this->blade('<x-ui.alert dismissible>x</x-ui.alert>');
    $input = (string) $this->blade('<x-forms.input name="q" label="Search" />');

    @unlink($dir.'/components/ui/icon.blade.php');
    @unlink($dir.'/components/forms/label.blade.php');

    expect($alert)->toContain('<svg')->not->toContain('APP-ICON')
        ->and($input)->toContain('for="input-q"')->not->toContain('APP-LABEL');
});

it('uses the private namespace in the package pagination view', function () {
    $paginator = new Illuminate\Pagination\LengthAwarePaginator(collect(range(1, 10)), 50, 10, 2, ['path' => '/users']);

    expect(view('blade-ui::pagination', ['paginator' => $paginator])->render())
        ->toContain('role="navigation"');
});
