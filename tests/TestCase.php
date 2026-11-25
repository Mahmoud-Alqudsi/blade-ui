<?php

namespace BladeUi\Tests;

use BladeUi\BladeUiServiceProvider;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use InteractsWithViews;

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.locale', 'ar');
        $app['config']->set('app.fallback_locale', 'en');
    }

    protected function getPackageProviders($app): array
    {
        return [BladeUiServiceProvider::class];
    }
}
