<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vite assets loaded by the layouts/guest component
    |--------------------------------------------------------------------------
    */
    'vite' => ['resources/css/app.css', 'resources/js/app.js'],

    /*
    |--------------------------------------------------------------------------
    | Font URL (set to null to disable the external font)
    |--------------------------------------------------------------------------
    */
    'font_url' => 'https://fonts.bunny.net/css?family=tajawal:400,500,700&display=swap',

    /*
    |--------------------------------------------------------------------------
    | Locales rendered right-to-left (dir="rtl") by the layout
    |--------------------------------------------------------------------------
    */
    'rtl_locales' => ['ar', 'fa', 'he', 'ur'],

    /*
    |--------------------------------------------------------------------------
    | Component prefix
    |--------------------------------------------------------------------------
    | null  : <x-ui.button> and <x-forms.input>           (default)
    | 'bui' : <x-bui::button> and <x-bui::input>          (plus grouped <x-bui::ui.button>)
    |
    | Use a prefix to avoid collisions with application components that share
    | the same names (e.g. components/ui/button.blade.php). Internal references
    | between package components always resolve through a fixed private namespace
    | and are unaffected by this setting. Update your templates if you change it.
    */
    'prefix' => env('BLADE_UI_PREFIX'),

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    | true: makes ->links() use the ui/pagination component throughout the application.
    */
    'pagination' => [
        'default' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Live component demo page
    |--------------------------------------------------------------------------
    | Enabled by default in the local environment. Can be forced with BLADE_UI_DEMO=true.
    */
    'demo' => [
        'enabled' => env('BLADE_UI_DEMO', env('APP_ENV') === 'local'),
        'path' => 'components-demo',
        'middleware' => ['web'],
    ],

];
