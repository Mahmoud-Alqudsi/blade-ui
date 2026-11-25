<?php

namespace BladeUi\Tests\Feature;

use BladeUi\Tests\TestCase;

class DemoRouteTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('blade-ui.demo.enabled', true);
    }

    public function test_the_demo_page_is_served_when_enabled(): void
    {
        $this->withoutVite()
            ->get('/components-demo')
            ->assertOk()
            ->assertSee('مكتبة مكونات Blade');
    }
}
