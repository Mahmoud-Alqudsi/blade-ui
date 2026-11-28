<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

/*
|--------------------------------------------------------------------------
| Browser test fixture generator
|--------------------------------------------------------------------------
| Renders the real Blade components into static HTML pages in tests/Browser/dist
| consumed by the Playwright suite. Runs only when BLADE_UI_FIXTURES=1 is set:
|     composer fixtures
*/
it('renders the browser fixture pages', function () {
    app()->setLocale('en');

    $dist = dirname(__DIR__, 3).'/tests/Browser/dist';
    @mkdir($dist, 0777, true);

    $errors = (new ViewErrorBag)->put('default', new MessageBag(['email' => ['The email address is invalid.']]));
    $paginator = new LengthAwarePaginator(collect(range(1, 10)), 120, 10, 6, ['path' => '/users']);

    view()->share('errors', $errors);

    $pages = [

        'modal' => <<<'BLADE'
            <x-ui.button x-on:click="$dispatch('open-modal-confirm')">Open modal</x-ui.button>
            <x-ui.button variant="outline" x-on:click="$dispatch('open-modal-locked')">Open locked</x-ui.button>
            <p>Content behind the modal.</p>

            <x-ui.modal name="confirm" title="Confirm action">
                <x-forms.input name="reason" label="Reason" />
                <x-slot:footer>
                    <x-ui.button variant="ghost" x-on:click="$dispatch('close-modal-confirm')">Cancel</x-ui.button>
                    <x-ui.button variant="danger">Delete</x-ui.button>
                </x-slot:footer>
            </x-ui.modal>

            <x-ui.modal name="locked" title="Terms" :closeable="false">
                Please accept to continue.
                <x-slot:footer>
                    <x-ui.button x-on:click="$dispatch('close-modal-locked')">Accept</x-ui.button>
                </x-slot:footer>
            </x-ui.modal>
            BLADE,

        'dropdown' => <<<'BLADE'
            <x-ui.dropdown>
                <x-slot:trigger><x-ui.button variant="outline">Account</x-ui.button></x-slot:trigger>
                <x-ui.dropdown-item href="#profile">Profile</x-ui.dropdown-item>
                <x-ui.dropdown-item>Settings</x-ui.dropdown-item>
                <x-ui.dropdown-item danger>Sign out</x-ui.dropdown-item>
            </x-ui.dropdown>
            <button type="button" id="outside" class="mt-40 block">Outside</button>
            BLADE,

        'combobox' => <<<'BLADE'
            <x-forms.combobox name="country" label="Country"
                :options="['sa' => 'Saudi Arabia', 'ae' => 'United Arab Emirates', 'eg' => 'Egypt', 'jo' => 'Jordan', 'ma' => 'Morocco', 'kw' => 'Kuwait']" />
            <button type="button" id="outside" class="mt-72 block">Outside</button>
            BLADE,

        'date-picker' => <<<'BLADE'
            <div class="space-y-6">
                <x-forms.date-picker name="due" label="Due date" value="2026-11-19" min="2026-11-10" max="2026-11-25" locale="en-US" :week-start="6" />
                <x-forms.date-picker name="start" label="Start date" locale="en-US" />
                <x-forms.date-picker name="required_date" label="Required date" locale="en-US" required />
            </div>
            <div class="h-96"></div>
            BLADE,

        'disclosure' => <<<'BLADE'
            <x-ui.tabs :items="['profile' => 'Profile', 'security' => 'Security', 'billing' => 'Billing']">
                <x-ui.tab-panel name="profile">Profile panel</x-ui.tab-panel>
                <x-ui.tab-panel name="security">Security panel</x-ui.tab-panel>
                <x-ui.tab-panel name="billing">Billing panel</x-ui.tab-panel>
            </x-ui.tabs>

            <h2 class="mt-8 mb-2 font-semibold">Single</h2>
            <x-ui.accordion>
                <x-ui.accordion-item title="Question one">Answer one</x-ui.accordion-item>
                <x-ui.accordion-item title="Question two">Answer two</x-ui.accordion-item>
            </x-ui.accordion>

            <h2 class="mt-8 mb-2 font-semibold">Multiple</h2>
            <x-ui.accordion multiple>
                <x-ui.accordion-item title="Multi one">Multi answer one</x-ui.accordion-item>
                <x-ui.accordion-item title="Multi two">Multi answer two</x-ui.accordion-item>
            </x-ui.accordion>
            BLADE,

        'drawer' => <<<'BLADE'
            <x-ui.button x-on:click="$dispatch('open-drawer-filters')">Open end drawer</x-ui.button>
            <x-ui.button variant="outline" x-on:click="$dispatch('open-drawer-menu')">Open start drawer</x-ui.button>

            <x-ui.drawer name="filters" title="Filters">
                <x-forms.switch name="active" label="Active only" />
                <x-slot:footer><x-ui.button x-on:click="$dispatch('close-drawer-filters')">Apply</x-ui.button></x-slot:footer>
            </x-ui.drawer>

            <x-ui.drawer name="menu" title="Menu" side="start">
                <x-ui.button variant="ghost" x-on:click="$dispatch('close-drawer-menu')">Close menu</x-ui.button>
            </x-ui.drawer>
            BLADE,

        'feedback' => <<<'BLADE'
            <div class="space-y-4">
                <x-ui.alert type="danger" title="Something failed" dismissible>Please try again.</x-ui.alert>
                <x-ui.alert type="info">Plain informational alert.</x-ui.alert>
                <x-ui.button x-on:click="$dispatch('toast', { type: 'success', title: 'Done', message: 'Saved successfully', duration: 600 })">Show toast</x-ui.button>
                <x-ui.button variant="outline" x-on:click="$dispatch('toast', { type: 'info', message: 'Sticky toast', duration: 60000 })">Show sticky toast</x-ui.button>
                <x-ui.theme-toggle />
            </div>
            <x-ui.toast />
            BLADE,

        'forms' => <<<'BLADE'
            <div class="space-y-5">
                <x-forms.input name="search" label="Search" placeholder="Search…">
                    <x-slot:icon-left><x-ui.icon name="magnifying-glass" /></x-slot:icon-left>
                </x-forms.input>
                <x-forms.input name="email" type="email" label="Email" value="not-an-email" hint="We never share it" required />
                <x-forms.password name="password" />
                <x-forms.select name="role" label="Role" placeholder="Choose a role" :options="['admin' => 'Admin', 'editor' => 'Editor']" />
                <x-forms.textarea name="bio" label="Bio" hint="Up to 300 characters" />
                <x-forms.checkbox name="terms" label="I agree to the terms" />
                <x-forms.radio name="plan" value="pro" label="Pro plan" />
                <x-forms.switch name="notify" label="Notifications" off-value="0" />
                <x-forms.file name="avatar" label="Avatar" accept="image/*" />
                <x-ui.tooltip text="Copy the link" position="bottom"><x-ui.button variant="outline">Copy</x-ui.button></x-ui.tooltip>
            </div>
            BLADE,

        'gallery' => <<<'BLADE'
            <div class="space-y-8">
                <section class="flex flex-wrap items-center gap-2">
                    <x-ui.button>Primary</x-ui.button>
                    <x-ui.button variant="secondary">Secondary</x-ui.button>
                    <x-ui.button variant="success">Success</x-ui.button>
                    <x-ui.button variant="danger">Danger</x-ui.button>
                    <x-ui.button variant="outline">Outline</x-ui.button>
                    <x-ui.button variant="ghost">Ghost</x-ui.button>
                    <x-ui.button loading>Loading</x-ui.button>
                </section>

                <section class="flex flex-wrap items-center gap-2">
                    <x-ui.badge>Neutral</x-ui.badge>
                    <x-ui.badge variant="brand" dot>Brand</x-ui.badge>
                    <x-ui.badge variant="success" dot>Success</x-ui.badge>
                    <x-ui.badge variant="warning" dot>Warning</x-ui.badge>
                    <x-ui.badge variant="danger" dot>Danger</x-ui.badge>
                    <x-ui.badge variant="info" dot>Info</x-ui.badge>
                    <x-ui.avatar name="Sara Ahmed" status="online" />
                    <x-ui.avatar />
                </section>

                <section class="space-y-3">
                    <x-ui.alert type="info" title="Info">Information</x-ui.alert>
                    <x-ui.alert type="success">Success</x-ui.alert>
                    <x-ui.alert type="warning">Warning</x-ui.alert>
                    <x-ui.alert type="danger">Danger</x-ui.alert>
                </section>

                <x-ui.card><x-slot:header> Card </x-slot:header> Body <x-slot:footer> Footer </x-slot:footer></x-ui.card>

                <section class="grid gap-4 sm:grid-cols-3">
                    <x-ui.stat-card label="Sales" value="12,500" change="+12%" trend="up" description="vs last month" icon="arrow-up" />
                    <x-ui.stat-card label="Refunds" value="38" change="-4%" trend="down" description="vs last month" icon="inbox" />
                    <x-ui.stat-card label="Visitors" value="9,240" description="this week" icon="user" />
                </section>

                <x-ui.breadcrumb :items="[['label' => 'Home', 'href' => '/'], ['label' => 'Users', 'href' => '/users'], ['label' => 'Sara']]" />
                <x-ui.steps :steps="['Account', 'Profile', 'Confirm']" :current="2" />

                <x-ui.table caption="Users" striped>
                    <x-slot:head><tr><th scope="col">Name</th><th scope="col">Role</th></tr></x-slot:head>
                    <tr><td>Sara Ahmed</td><td>Admin</td></tr>
                    <tr><td>Khaled Ali</td><td>Editor</td></tr>
                </x-ui.table>

                <x-ui.pagination :paginator="$paginator" />

                <section class="space-y-4">
                    <x-ui.progress :value="65" label="Profile completion" show-value />
                    <x-ui.progress :value="80" variant="success" label="Storage" show-value />
                    <x-ui.progress :value="40" variant="warning" />
                    <x-ui.progress :value="25" variant="danger" label="Errors" />
                    <x-ui.progress indeterminate label="Processing" />
                    <x-ui.skeleton shape="text" :lines="3" />
                    <x-ui.empty-state title="No projects yet" description="Create your first project."><x-ui.button size="sm">New project</x-ui.button></x-ui.empty-state>
                </section>
            </div>
            BLADE,
    ];

    $page = fn (string $title, string $body) => '<!DOCTYPE html><html lang="en" dir="ltr"><head><meta charset="utf-8">'
        .'<meta name="viewport" content="width=device-width, initial-scale=1"><title>'.$title.'</title>'
        .'<link rel="stylesheet" href="/app.css"><script defer src="/app.js"></script></head>'
        .'<body x-data class="min-h-screen bg-white p-6 text-slate-900 dark:bg-slate-950 dark:text-slate-100">'
        .'<main class="mx-auto max-w-3xl">'.$body.'</main></body></html>';

    foreach ($pages as $name => $template) {
        $html = Blade::render($template, ['errors' => $errors, 'paginator' => $paginator]);

        file_put_contents("{$dist}/{$name}.html", $page(ucfirst($name), $html));

        expect(file_exists("{$dist}/{$name}.html"))->toBeTrue()
            ->and(strlen($html))->toBeGreaterThan(50);
    }

    view()->share('errors', new ViewErrorBag);
})->skip(! getenv('BLADE_UI_FIXTURES'), 'Set BLADE_UI_FIXTURES=1 (composer fixtures) to generate browser test fixture pages.');
