<?php

namespace BladeUi;

use BladeUi\Console\InstallCommand;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BladeUiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom($this->root('config/blade-ui.php'), 'blade-ui');
    }

    public function boot(): void
    {
        // Package views (demo and pagination): view('blade-ui::demo')
        $this->loadViewsFrom($this->root('resources/views'), 'blade-ui');

        // Anonymous components: <x-ui.button>, <x-forms.input>, and <x-layouts.guest> without publishing.
        Blade::anonymousComponentPath($this->root('resources/components'));

        if (config('blade-ui.pagination.default')) {
            Paginator::defaultView('blade-ui::pagination');
            Paginator::defaultSimpleView('blade-ui::pagination');
        }

        $this->registerDemoRoute();

        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);
            $this->registerPublishing();
        }
    }

    protected function registerDemoRoute(): void
    {
        if (! config('blade-ui.demo.enabled') || $this->app->routesAreCached()) {
            return;
        }

        Route::middleware(config('blade-ui.demo.middleware', ['web']))
            ->get(config('blade-ui.demo.path', 'components-demo'), fn () => view('blade-ui::demo'))
            ->name('blade-ui.demo');
    }

    protected function registerPublishing(): void
    {
        $this->publishes([
            $this->root('config/blade-ui.php') => config_path('blade-ui.php'),
        ], 'blade-ui-config');

        // Copy components into the application for customization.
        $this->publishes([
            $this->root('resources/components') => resource_path('views/components'),
        ], 'blade-ui-components');

        $this->publishes([
            $this->root('resources/views') => resource_path('views/vendor/blade-ui'),
        ], 'blade-ui-views');

        $this->publishes([
            $this->root('stubs/login.blade.php') => resource_path('views/auth/login.blade.php'),
        ], 'blade-ui-login');
    }

    protected function root(string $path = ''): string
    {
        return dirname(__DIR__).($path === '' ? '' : DIRECTORY_SEPARATOR.$path);
    }
}
