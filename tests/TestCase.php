<?php

namespace BladeUi\Tests;

use BladeUi\BladeUiServiceProvider;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use InteractsWithViews;

    protected function getPackageProviders($app): array
    {
        return [BladeUiServiceProvider::class];
    }
}
