<?php

namespace BladeUi\Tests\Feature;

use BladeUi\Support\ClassMerger;
use BladeUi\Support\PassthroughMerger;
use BladeUi\Tests\TestCase;

class ClassMergingDisabledTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('blade-ui.merge_classes', false);
    }

    public function test_the_passthrough_merger_is_bound(): void
    {
        $this->assertInstanceOf(PassthroughMerger::class, $this->app->make(ClassMerger::class));
    }

    public function test_conflicting_classes_are_kept_when_merging_is_disabled(): void
    {
        $tokens = bladeUiClassTokens((string) $this->blade('<x-ui.button class="bg-red-500">x</x-ui.button>'));

        $this->assertContains('bg-brand-600', $tokens);
        $this->assertContains('bg-red-500', $tokens);
    }
}
