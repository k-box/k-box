<?php

namespace Tests\Feature\Plugins;

use KBox\User;
use KBox\Flags;
use Tests\TestCase;
use KBox\Capability;
use KBox\Plugins\PluginManager;
use KBox\Plugins\PluginManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Klink\DmsAdapter\Traits\SwapInstance;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PluginsControllerTest extends TestCase
{
    use SwapInstance;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('app');

        $pluginManifest = 'plugins.php';
        $enabledPluginManifest = 'enabled-plugins.php';

        $manifest = new PluginManifest(
            new Filesystem,
            base_path('/tests/fixtures/plugins/'),
            Storage::disk('app')->path($pluginManifest)
        );
        $manager = new PluginManager(
            $manifest,
            new Filesystem,
            Storage::disk('app')->path($enabledPluginManifest)
        );

        $this->swap(PluginManifest::class, $manifest);
        $this->swap(PluginManager::class, $manager);
    }

    public function test_non_admins_cannot_view_plugins_page()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PROJECT_MANAGER);
        });

        $url = route('administration.plugins.index');

        $response = $this->actingAs($user)->get($url);
        
        $response->assertSuccessful();
        $response->assertViewIs('errors.403');
    }

    public function test_discovered_plugins_are_presented()
    {
        Flags::enable(Flags::PLUGINS);
        
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $url = route('administration.plugins.index');

        $response = $this->actingAs($user)->get($url);
        
        $response->assertSuccessful();
        $response->assertViewIs('plugins.index');
        $response->assertViewHas('pagetitle', trans('plugins.page_title'));
        $response->assertViewHas('plugins');
        $response->assertSee('k-box-unittest-demo-plugin');
        $response->assertSee(trans('plugins.actions.enable'));
        $this->assertEquals(1, $response->data('plugins')->count());
    }

    public function test_plugin_controller_can_enable_plugins()
    {
        $this->withoutMiddleware();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $url = route('administration.plugins.update', 'k-box-unittest-demo-plugin');

        $response = $this->actingAs($user)->put($url);
        
        $response->assertRedirect(route('administration.plugins.index'));
        $response->assertSessionHas('flash_message', trans('plugins.messages.enabled'));
        
        $manager = resolve(PluginManager::class);
        $this->assertEquals(1, $manager->enabled()->count());
        $this->assertNotNull($manager->enabled()->get('k-box-unittest-demo-plugin'));
    }

    public function test_plugin_controller_can_disable_plugins()
    {
        $this->withoutMiddleware();
        $manager = resolve(PluginManager::class);

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $manager->enable('k-box-unittest-demo-plugin');

        $url = route('administration.plugins.destroy', 'k-box-unittest-demo-plugin');

        $response = $this->actingAs($user)->delete($url);
        
        $response->assertRedirect(route('administration.plugins.index'));
        $response->assertSessionHas('flash_message', trans('plugins.messages.disabled'));
        
        $this->assertEquals(0, $manager->enabled()->count());
    }
}
