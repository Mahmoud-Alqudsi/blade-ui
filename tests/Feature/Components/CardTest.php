<?php

it('renders header, body and footer slots', function () {
    $this->blade('<x-ui.card><x-slot:header>الرأس</x-slot:header>المحتوى<x-slot:footer>التذييل</x-slot:footer></x-ui.card>')
        ->assertSeeInOrder(['الرأس', 'المحتوى', 'التذييل']);
});

it('omits header and footer when they are not provided', function () {
    $this->blade('<x-ui.card>فقط المحتوى</x-ui.card>')
        ->assertSee('فقط المحتوى')
        ->assertDontSee('border-b border-slate-200', false)
        ->assertDontSee('border-t border-slate-200', false);
});

it('supports hoverable and padding options', function () {
    $this->blade('<x-ui.card hoverable padding="lg">نص</x-ui.card>')
        ->assertSee('hover:shadow-lg', false)
        ->assertSee('p-8', false);
});
