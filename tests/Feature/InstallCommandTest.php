<?php

beforeEach(function () {
    $this->css = tempnam(sys_get_temp_dir(), 'bui-css-');
});

afterEach(function () {
    @unlink($this->css);
});

it('adds the package import right after the tailwind import', function () {
    file_put_contents($this->css, "@import \"tailwindcss\";\n\n@theme {\n}\n");

    $this->artisan('blade-ui:install', ['--css' => $this->css])->assertSuccessful();

    $lines = explode("\n", file_get_contents($this->css));

    expect($lines[0])->toBe('@import "tailwindcss";')
        ->and($lines[1])->toContain('blade-ui/resources/css/blade-ui.css');
});

it('is idempotent', function () {
    file_put_contents($this->css, "@import 'tailwindcss';\n");

    $this->artisan('blade-ui:install', ['--css' => $this->css])->assertSuccessful();
    $this->artisan('blade-ui:install', ['--css' => $this->css])->assertSuccessful();

    expect(substr_count(file_get_contents($this->css), 'blade-ui.css'))->toBe(1);
});

it('leaves the file untouched when there is no tailwind import', function () {
    file_put_contents($this->css, "body { color: red; }\n");

    $this->artisan('blade-ui:install', ['--css' => $this->css])->assertSuccessful();

    expect(file_get_contents($this->css))->toBe("body { color: red; }\n");
});

it('succeeds even when the css file does not exist', function () {
    $this->artisan('blade-ui:install', ['--css' => '/tmp/definitely-missing-file.css'])->assertSuccessful();
});

it('computes the import path relative to the css file location', function () {
    $dir = base_path('resources/css');
    @mkdir($dir, 0777, true);
    $file = $dir.'/bui-test-app.css';
    file_put_contents($file, "@import \"tailwindcss\";\n");

    $this->artisan('blade-ui:install', ['--css' => $file])->assertSuccessful();

    $package = json_decode(file_get_contents(dirname(__DIR__, 2).'/composer.json'), true)['name'];

    expect(file_get_contents($file))->toContain("@import '../../vendor/{$package}/resources/css/blade-ui.css';");

    @unlink($file);
});
