<?php

namespace BladeUi\Support;

/** Concatenates class lists in order without conflict resolution (Laravel's default behavior). */
final class PassthroughMerger implements ClassMerger
{
    public function merge(string ...$classes): string
    {
        return trim(implode(' ', array_filter(array_map('trim', $classes), 'strlen')));
    }
}
