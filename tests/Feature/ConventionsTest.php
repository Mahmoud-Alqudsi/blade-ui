<?php

$root = dirname(__DIR__, 2);

/** Inspected component files with Blade comments stripped (comments include public usage examples). */
$componentSources = function () use ($root): array {
    $sources = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/resources/components')) as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            $sources[$file->getPathname()] = preg_replace('/\{\{--.*?--\}\}/s', '', file_get_contents($file->getPathname()));
        }
    }

    $sources[$root.'/resources/views/pagination.blade.php'] = preg_replace(
        '/\{\{--.*?--\}\}/s', '', file_get_contents($root.'/resources/views/pagination.blade.php')
    );

    return $sources;
};

it('never references sibling components through public names', function () use ($componentSources) {
    foreach ($componentSources() as $path => $source) {
        expect(preg_match('~</?x-(ui|forms|layouts)\.~', $source))
            ->toBe(0, basename($path).' references a sibling component by its public name; use <x-blade-ui::ui.icon> or similar instead.');
    }
});


it('keeps component names unique across groups so flat prefixed names never collide', function () use ($root) {
    $names = [];

    foreach (['ui', 'forms', 'layouts'] as $group) {
        foreach (glob("{$root}/resources/components/{$group}/*.blade.php") as $file) {
            $names[] = basename($file, '.blade.php');
        }
    }

    expect($names)->not->toBeEmpty()
        ->and(array_diff_assoc($names, array_unique($names)))->toBe([]);
});
