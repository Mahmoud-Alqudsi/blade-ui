<?php

namespace BladeUi;

use BladeUi\Console\InstallCommand;
use BladeUi\Support\TagPrefixer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BladeUiServiceProvider extends ServiceProvider
{
    /**
     * Private namespace used by package components to reference one another (<x-blade-ui::ui.icon>).
     * Always fixed: unaffected by the configured prefix or by application components with the same names.
     */
    public const INTERNAL_NAMESPACE = 'blade-ui';

    /** View namespace pointing to the components directory (target for component aliases). */
    public const COMPONENT_VIEWS = 'blade-ui-components';

    public function register(): void
    {
        $this->mergeConfigFrom($this->root('config/blade-ui.php'), 'blade-ui');
    }

    public function boot(): void
    {
        // Package views (demo and pagination): view('blade-ui::demo')
        $this->loadViewsFrom($this->root('resources/views'), 'blade-ui');

        // Translations: __('blade-ui::messages.close')
        $this->loadTranslationsFrom($this->root('lang'), 'blade-ui');

        $this->registerComponents();

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

    /**
     * Without prefix: <x-ui.button> and <x-forms.input>         (anonymous component path)
     * Prefix "bui":   <x-bui::button> and <x-bui::ui.button>    (explicit aliases, no unprefixed tags)
     *
     * Explicit component aliases (Blade::component) are used in prefix mode instead of anonymous
     * component paths: an alias matches literally and is checked first, so it never hijacks a generic
     * application tag such as <x-button>, and an application component cannot shadow an internal reference.
     */
    protected function registerComponents(): void
    {
        $root = $this->root('resources/components');
        $prefix = TagPrefixer::normalize(config('blade-ui.prefix'));

        // View namespace for package components: view('blade-ui-components::ui.button')
        $this->loadViewsFrom($root, self::COMPONENT_VIEWS);

        foreach ($this->discoverComponents($root) as ['group' => $group, 'name' => $name]) {
            $view = self::COMPONENT_VIEWS."::{$group}.{$name}";

            // Always register fixed internal references: <x-blade-ui::ui.icon>
            Blade::component($view, self::INTERNAL_NAMESPACE."::{$group}.{$name}");

            if ($prefix !== null) {
                Blade::component($view, "{$prefix}::{$name}");
                Blade::component($view, "{$prefix}::{$group}.{$name}");
            }
        }

        if ($prefix === null) {
            Blade::anonymousComponentPath($root);
        }
    }

    /** @return list<array{group: string, name: string}> */
    protected function discoverComponents(string $root): array
    {
        $components = [];

        foreach (TagPrefixer::GROUPS as $group) {
            foreach (glob($root.DIRECTORY_SEPARATOR.$group.DIRECTORY_SEPARATOR.'*.blade.php') ?: [] as $file) {
                $components[] = ['group' => $group, 'name' => basename($file, '.blade.php')];
            }
        }

        return $components;
    }

    protected function registerDemoRoute(): void
    {
        if (! config('blade-ui.demo.enabled') || $this->app->routesAreCached()) {
            return;
        }

        Route::middleware(config('blade-ui.demo.middleware', ['web']))
            ->get(config('blade-ui.demo.path', 'components-demo'), function () {
                // Keep the demo view written in default syntax and rewrite tags to the configured prefix at render time.
                $source = (string) file_get_contents(view()->getFinder()->find('blade-ui::demo'));

                return Blade::render(TagPrefixer::rewrite($source, config('blade-ui.prefix')));
            })
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
            $this->root('lang') => $this->app->langPath('vendor/blade-ui'),
        ], 'blade-ui-lang');

        $this->publishes([
            $this->root('stubs/login.blade.php') => resource_path('views/auth/login.blade.php'),
        ], 'blade-ui-login');
    }

    protected function root(string $path = ''): string
    {
        return dirname(__DIR__).($path === '' ? '' : DIRECTORY_SEPARATOR.$path);
    }
}
