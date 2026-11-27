{{--
| Sign-in page built from package components without duplicating HTML.
| Expected route: route('login') (GET displays the page; POST handles the form).
|
| Note: This template uses the default <x-ui.*>, <x-forms.*>, and <x-layouts.*> tags.
| If you configured blade-ui.prefix (e.g. "bui"), rewrite the tags after publishing:
|   sed -i -E 's#<(/?)x-(ui|forms|layouts)\.#<\1x-bui::\2.#g' resources/views/auth/login.blade.php
--}}
<x-layouts.guest title="تسجيل الدخول">
    <x-ui.card padding="lg" class="w-full max-w-md">
        <x-slot:header>
            <div class="flex items-center justify-between gap-3">
                <span class="text-lg">تسجيل الدخول</span>
                <x-ui.badge variant="brand" size="sm">{{ config('app.name') }}</x-ui.badge>
            </div>
        </x-slot:header>

        <div class="space-y-5">
            @if (session('status'))
                <x-ui.alert type="success" dismissible>{{ session('status') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert type="danger" title="تعذّر تسجيل الدخول" dismissible>
                    راجع الحقول المحددة أدناه ثم أعد المحاولة.
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5"
                  x-data="{ submitting: false }" x-on:submit="submitting = true">
                @csrf

                <x-forms.input name="email" type="email" label="البريد الإلكتروني" placeholder="name@example.com"
                               autocomplete="email" autofocus required>
                    <x-slot:icon-left><x-ui.icon name="mail" /></x-slot:icon-left>
                </x-forms.input>

                <x-forms.password name="password" autocomplete="current-password" required />

                <div class="flex items-center justify-between gap-4">
                    <x-forms.checkbox name="remember" label="تذكرني" />
                    <a href="{{ Route::has('password.request') ? route('password.request') : '#' }}"
                       class="text-sm font-medium text-brand-600 hover:underline focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500 dark:text-brand-400">
                        نسيت كلمة المرور؟
                    </a>
                </div>

                <x-ui.button type="submit" block size="lg" loading-when="submitting">دخول</x-ui.button>
            </form>
        </div>

        <x-slot:footer>
            <p class="text-center text-slate-600 dark:text-slate-400">
                ليس لديك حساب؟
                <a href="{{ Route::has('register') ? route('register') : '#' }}"
                   class="font-medium text-brand-600 hover:underline dark:text-brand-400">إنشاء حساب</a>
            </p>
        </x-slot:footer>
    </x-ui.card>
</x-layouts.guest>
