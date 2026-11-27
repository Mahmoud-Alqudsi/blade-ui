<?php

namespace BladeUi\Support;

use Illuminate\Contracts\Container\Container;
use RuntimeException;

/**
 * Resolves Tailwind CSS v4 class conflicts via stamat/tailwind-merge-php (optional).
 *
 * Note: gehrisandro/tailwind-merge-php only targets Tailwind v3 and is not used here
 * because package components use v4 utilities (such as shadow-xs and outline-hidden).
 * To use a different merger, bind the ClassMerger interface in the Laravel container.
 */
final class TailwindClassMerger implements ClassMerger
{
    public const DRIVER = 'Stamat\\TailwindMerge\\TailwindMerge';

    public const PACKAGE = 'stamat/tailwind-merge-php';

    public function __construct(private readonly object $driver) {}

    public static function isInstalled(): bool
    {
        return class_exists(self::DRIVER);
    }

    /**
     * Determines whether to enable Tailwind class merging: 'auto' (when installed), true (required), or false (disabled).
     *
     * @throws RuntimeException When class merging is explicitly required without the package installed.
     */
    public static function shouldUse(mixed $mode, bool $installed): bool
    {
        if ($mode === false || $mode === 0 || in_array($mode, ['false', '0', 'off', 'no'], true)) {
            return false;
        }

        if ($mode === true || in_array($mode, ['true', '1', 'on', 'yes'], true)) {
            if (! $installed) {
                throw new RuntimeException(
                    'blade-ui.merge_classes is set to true, but '.self::PACKAGE.' is not installed. '.
                    'Run: composer require '.self::PACKAGE.' or set the option to "auto".'
                );
            }

            return true;
        }

        return $installed;
    }

    /** Resolves the driver from the Laravel container if bound, or falls back to the default shared instance. */
    public static function make(Container $app): self
    {
        $driver = self::DRIVER;

        return new self($app->bound($driver) ? $app->make($driver) : $driver::instance());
    }

    public function merge(string ...$classes): string
    {
        return trim((string) $this->driver->merge(...array_filter($classes, 'strlen')));
    }
}
