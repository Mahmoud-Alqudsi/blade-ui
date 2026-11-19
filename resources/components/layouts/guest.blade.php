{{--
|--------------------------------------------------------------------------
| layouts/guest — public page layout (sign-in, demos, etc.)
|--------------------------------------------------------------------------
| Props: title, centered (vertically center the content)
| Sets lang and dir from the application locale and applies dark mode before rendering to prevent a flash.
| Settings (config/blade-ui.php): vite, font_url, rtl_locales
--}}
@props(['title' => null, 'centered' => true])

@php
    $locale = app()->getLocale();
    $dir = in_array($locale, config('blade-ui.rtl_locales', ['ar', 'fa', 'he', 'ur']), true) ? 'rtl' : 'ltr';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $dir }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' — ' : '' }}{{ config('app.name') }}</title>

    <script>
        (() => {
            const saved = localStorage.getItem('theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @if ($fontUrl = config('blade-ui.font_url'))
        <link rel="preconnect" href="{{ parse_url($fontUrl, PHP_URL_SCHEME) }}://{{ parse_url($fontUrl, PHP_URL_HOST) }}">
        <link href="{{ $fontUrl }}" rel="stylesheet">
    @endif

    @vite(config('blade-ui.vite', ['resources/css/app.css', 'resources/js/app.js']))
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="absolute end-4 top-4">
        <x-ui.theme-toggle />
    </div>

    <main @class(['min-h-screen p-4', 'flex items-center justify-center' => $centered])>
        {{ $slot }}
    </main>

    <x-ui.toast />
</body>
</html>
