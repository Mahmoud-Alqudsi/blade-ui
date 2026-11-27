<?php

namespace BladeUi\Support;

/**
 * Merges CSS class lists and resolves conflicts between them.
 *
 * Component classes are passed first and caller classes last, so later classes win.
 * You can swap the implementation by binding this interface in the Laravel container:
 *
 *     $this->app->singleton(\BladeUi\Support\ClassMerger::class, MyMerger::class);
 */
interface ClassMerger
{
    public function merge(string ...$classes): string;
}
