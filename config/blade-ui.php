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
