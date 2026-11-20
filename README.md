# Blade UI

[![Tests](https://github.com/Mahmoud-Alqudsi/blade-ui/actions/workflows/tests.yml/badge.svg)](https://github.com/Mahmoud-Alqudsi/blade-ui/actions/workflows/tests.yml)
[![Packagist](https://img.shields.io/packagist/v/mahmoud-alqudsi/blade-ui.svg)](https://packagist.org/packages/mahmoud-alqudsi/blade-ui)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

> A collection of production-ready Blade components for Laravel 13, built with Tailwind CSS v4 and Alpine.js. Blade UI includes RTL and dark mode support, accessible markup, and components that work with Livewire.

- 26 UI components, 11 form components, and a guest page layout
- Components are available immediately after installation; publishing files is optional
- An Artisan command connects the package stylesheet to your application
- Pest tests run with Orchestra Testbench

## Requirements

- PHP 8.3 through 8.x
- Laravel 13
- Tailwind CSS 4 with `@tailwindcss/vite`
- Alpine.js 3 and `@alpinejs/focus`

## Installation

Install the package and run its setup command:

```bash
composer require mahmoud-alqudsi/blade-ui
php artisan blade-ui:install
```

The command adds the package stylesheet import after `@import "tailwindcss";` in `resources/css/app.css`. It also checks `package.json` and prints an install command if required npm packages are missing. The dependencies are:

```bash
npm install -D tailwindcss @tailwindcss/vite
npm install alpinejs @alpinejs/focus
```

Register Alpine's Focus plugin in `resources/js/app.js` before starting Alpine:

```js
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();
```

The Focus plugin enables focus management in interactive components such as dialogs, drawers, and menus. If Livewire starts Alpine for your application, do not call `Alpine.start()` again. Register the plugin during Alpine's initialization instead:

```js
import focus from '@alpinejs/focus';

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(focus);
});
```

Build your frontend assets with your application's usual Vite command, for example:

```bash
npm run dev
```

In the local environment, the component demo is available at `/components-demo` by default.

## Usage

Components use Laravel's anonymous Blade component syntax:

```blade
<x-ui.button variant="danger" size="sm">Delete</x-ui.button>

<x-forms.input
    name="email"
    type="email"
    label="Email"
    hint="We will not share your address."
    required
>
    <x-slot:icon-left><x-ui.icon name="mail" /></x-slot:icon-left>
</x-forms.input>

<x-ui.modal name="confirm" title="Confirm">Continue?</x-ui.modal>
<x-ui.button x-on:click="$dispatch('open-modal-confirm')">Open dialog</x-ui.button>
```

Component props, slots, and examples are documented in English comments at the top of each file under `resources/components`.

| Directory | Components |
|---|---|
| `ui/` | `accordion`, `accordion-item`, `alert`, `avatar`, `badge`, `breadcrumb`, `button`, `card`, `dropdown`, `dropdown-item`, `drawer`, `empty-state`, `icon`, `modal`, `pagination`, `progress`, `skeleton`, `spinner`, `stat-card`, `steps`, `tab-panel`, `table`, `tabs`, `theme-toggle`, `toast`, `tooltip` |
| `forms/` | `checkbox`, `combobox`, `date-picker`, `file`, `input`, `label`, `password`, `radio`, `select`, `switch`, `textarea` |
| `layouts/` | `guest` |

## Configuration and customization

Publish only the files you need to customize:

```bash
php artisan vendor:publish --tag=blade-ui-config
php artisan vendor:publish --tag=blade-ui-components
php artisan vendor:publish --tag=blade-ui-views
php artisan vendor:publish --tag=blade-ui-login
```

These tags publish the package configuration to `config/blade-ui.php`, components to `resources/views/components`, package views to `resources/views/vendor/blade-ui`, and the login page stub to `resources/views/auth/login.blade.php`.

The main configuration options are:

| Option | Purpose |
|---|---|
| `vite` | Entry points loaded by the `layouts/guest` component |
| `font_url` | External font stylesheet URL; set to `null` to disable it |
| `rtl_locales` | Locales rendered with `dir="rtl"` by the guest layout |
| `pagination.default` | When `true`, Laravel pagination uses the package pagination view by default |
| `demo.enabled` | Enables or disables the component demo route |
| `demo.path` | URI path for the component demo route |
| `demo.middleware` | Middleware applied to the component demo route |

The demo route is enabled by default in the local environment. Set `BLADE_UI_DEMO=true` to enable it in another environment, or `BLADE_UI_DEMO=false` to disable it.

To change the primary palette, override the `--color-brand-*` variables in your CSS `@theme` block after importing the package stylesheet. To customize a component, publish the components and edit the copy in your application.

## Local development

To try the package from a neighboring directory, add a path repository to your application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../blade-ui",
            "options": { "symlink": false }
        }
    ]
}
```

Then require the development version:

```bash
composer require mahmoud-alqudsi/blade-ui:@dev
```

Using `symlink: false` makes the package files available for Tailwind CSS source scanning in setups that do not scan symlinked directories.

## Tests

Install the development dependencies and run the Pest suite:

```bash
composer install
composer test
```

## Troubleshooting

- **Components render without styles:** Confirm that `blade-ui.css` is imported after `@import "tailwindcss";` in the CSS entry point passed to `blade-ui:install`.
- **Tailwind does not generate component utility classes:** The package stylesheet includes Tailwind v4 `@source` directives for the package components and views. Make sure your application processes the imported stylesheet with Tailwind CSS v4.
- **Dialogs or menus do not manage focus:** Install and register `@alpinejs/focus` as described above.
- **Dark mode does not apply:** The package styles use the `dark` class on the root `<html>` element. The guest layout initializes that class from the saved theme or the user's system preference; custom layouts should provide equivalent theme handling.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for development and contribution guidelines. Please also read the [Code of Conduct](CODE_OF_CONDUCT.md). To report a security issue, see [SECURITY.md](SECURITY.md).

## License

Blade UI is open-source software licensed under the [MIT license](LICENSE).
