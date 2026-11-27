<?php

namespace BladeUi\Tests\Feature;

use BladeUi\Tests\TestCase;
use Illuminate\Pagination\LengthAwarePaginator;

class PrefixedComponentsTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('blade-ui.prefix', 'bui');
        $app['config']->set('blade-ui.demo.enabled', true);
    }

    public function test_flat_prefixed_names_resolve(): void
    {
        $this->blade('<x-bui::button variant="danger">x</x-bui::button>')->assertSee('bg-red-600', false);
        $this->blade('<x-bui::input name="q" />')->assertSee('id="input-q"', false);
        $this->blade('<x-bui::empty-state title="Nothing here" />')->assertSee('Nothing here');
    }

    public function test_grouped_prefixed_names_resolve(): void
    {
        $this->blade('<x-bui::ui.button variant="danger">x</x-bui::ui.button>')->assertSee('bg-red-600', false);
        $this->blade('<x-bui::forms.input name="q" />')->assertSee('id="input-q"', false);
    }

    public function test_the_unprefixed_names_are_not_registered(): void
    {
        $threw = false;

        try {
            $this->blade('<x-ui.button>x</x-ui.button>');
        } catch (\Throwable $e) {
            $threw = true;
            $this->assertStringContainsString('ui.button', $e->getMessage());
        }

        $this->assertTrue($threw, 'The unprefixed <x-ui.button> tag should not be available when a prefix is configured.');
    }

    public function test_a_project_component_with_the_same_name_is_not_hijacked(): void
    {
        $dir = sys_get_temp_dir().'/bui-project-'.uniqid();
        mkdir($dir.'/components/ui', 0777, true);
        file_put_contents($dir.'/components/ui/button.blade.php', 'APP-BUTTON');
        file_put_contents($dir.'/components/button.blade.php', 'APP-FLAT-BUTTON');

        view()->getFinder()->prependLocation($dir);

        $projectGrouped = (string) $this->blade('<x-ui.button>x</x-ui.button>');
        $projectFlat = (string) $this->blade('<x-button>x</x-button>');
        $package = (string) $this->blade('<x-bui::button>x</x-bui::button>');

        @unlink($dir.'/components/ui/button.blade.php');
        @unlink($dir.'/components/button.blade.php');

        $this->assertStringContainsString('APP-BUTTON', $projectGrouped);
        $this->assertStringContainsString('APP-FLAT-BUTTON', $projectFlat);
        $this->assertStringContainsString('bg-brand-600', $package);
        $this->assertStringNotContainsString('APP-', $package);
    }

    public function test_components_that_use_other_components_still_work(): void
    {
        $this->blade('<x-bui::password name="password" />')->assertSee('id="input-password"', false);
        $this->blade('<x-bui::alert dismissible>x</x-bui::alert>')->assertSee('<svg', false);
        $this->blade('<x-bui::stat-card label="Sales" value="5" icon="user" />')->assertSee('Sales');
    }

    public function test_the_pagination_view_works_with_a_prefix(): void
    {
        $paginator = new LengthAwarePaginator(collect(range(1, 10)), 50, 10, 2, ['path' => '/users']);

        $this->assertStringContainsString('role="navigation"', view('blade-ui::pagination', ['paginator' => $paginator])->render());
    }

    public function test_the_demo_page_is_rewritten_to_the_configured_prefix(): void
    {
        $this->withoutVite()
            ->get('/components-demo')
            ->assertOk()
            ->assertSee('مكتبة مكونات Blade');
    }
}
