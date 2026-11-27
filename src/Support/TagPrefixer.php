<?php

namespace BladeUi\Support;

use InvalidArgumentException;

final class TagPrefixer
{
    /** Component groups (subdirectories under resources/components). */
    public const GROUPS = ['ui', 'forms', 'layouts'];

    /**
     * Validates and returns the normalized prefix, or null when no prefix is configured.
     *
     * @throws InvalidArgumentException
     */
    public static function normalize(mixed $prefix): ?string
    {
        if ($prefix === null || $prefix === '' || $prefix === false) {
            return null;
        }

        if (! is_string($prefix) || ! preg_match('/^[a-z][a-z0-9-]*$/i', $prefix)) {
            throw new InvalidArgumentException(
                'Invalid blade-ui.prefix value: it must start with a letter and contain only letters, numbers, and hyphens (e.g. "bui").'
            );
        }

        return $prefix;
    }

    /**
     * Rewrites default component tags (<x-ui.button>) to prefixed tags (<x-bui::ui.button>).
     * Slot tags (<x-slot:...>) are left untouched.
     */
    public static function rewrite(string $blade, mixed $prefix): string
    {
        $prefix = self::normalize($prefix);

        if ($prefix === null) {
            return $blade;
        }

        return (string) preg_replace(
            '~<(/?)x-('.implode('|', self::GROUPS).')\.~',
            '<${1}x-'.$prefix.'::${2}.',
            $blade
        );
    }
}
