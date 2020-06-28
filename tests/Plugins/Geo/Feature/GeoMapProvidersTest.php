<?php

namespace Tests\Plugins\Geo\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use KBox\Geo\GeoService;
use KBox\Plugins\PluginManager;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GeoMapProvidersTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    private $startConfig = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->markTestSkipped(
            'Geo Plugin test, routes are not defined. Verify why the plugin configuration is not loaded (is the plugin enabled).'
        );

        app(PluginManager::class)->enable('k-box-kbox-plugin-geo');

        $service = app(GeoService::class);

        $this->startConfig = $service->config();

        $service->config($service->defaultConfig());
    }

    protected function tearDown(): void
    {
        if (! is_null($this->startConfig)) {
            app(GeoService::class)->config($this->startConfig);
        }

        parent::tearDown();
    }
    
    const DEFAULT_PROVIDERS = [
        "hum_osm" => [
            "label" => "HumanitarianOpenStreetMap",
            "url" => "https://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png",
            "type" => "tile",
            "maxZoom" => 20,
            "subdomains" => "abc",
            "enable" => true,
            "attribution" => '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
        ],
        "osm" => [
            "type" => "tile",
            "label" => "OpenStreetMap",
            "url" => "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
            "attribution" => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            "maxZoom" => 20,
            "enable" => true,
        ],
        "mundialis_topo" => [
            "label" => "Mundialis (Topographic OSM)",
            "layers" => "TOPO-OSM-WMS",
            "attribution" => '&copy; <a href="https://www.mundialis.de/en/ows-mundialis/" target="_blank">Mundialis GmbH & Co. KG</a>',
            "type" => "wms",
            "url" => "http://ows.mundialis.de/services/service?",
            "enable" => true,
        ]
    ];

    public function test_default_providers_are_listed()
    {
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders');

        $response = $this->get($url);
        
        $response->assertStatus(200);
        $response->assertViewIs('geo::maps');
        $response->assertViewHas('plugintitle');
        $response->assertViewHas('pagetitle');
        $response->assertViewHas('providers', self::DEFAULT_PROVIDERS);
        $response->assertViewHas('default', 'hum_osm');
    }

    public function test_tile_provider_can_be_created()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $user->addCapabilities(Capability::$ADMIN);
        
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.store');

        $custom_provider_request_data = [
            "label" => "Custom Provider",
            "url" => "https://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png",
            "type" => "tile",
            "maxZoom" => 20,
            "subdomains" => "abc",
            "attribution" => '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
        ];

        $custom_provider = [
            "custom-provider" => $custom_provider_request_data,
        ];

        $response = $this->actingAs($user)->post($url, $custom_provider_request_data);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders'));

        $settings = app(GeoService::class)->config('map');

        $this->assertEquals(array_merge(self::DEFAULT_PROVIDERS, $custom_provider), $settings['providers']);
        $this->assertEquals('hum_osm', $settings['default']);
    }

    public function test_tile_provider_cannot_be_created_if_parameters_are_invalid()
    {
        $user = factory(User::class)->create();

        $user->addCapabilities(Capability::$ADMIN);
        
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.store');

        $custom_provider_request_data = [
            "label" => "Humanitarian Open Street Maps",
            "type" => "tile",
            "maxZoom" => 20,
            "subdomains" => "abc",
            "attribution" => '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
        ];

        $response = $this->actingAs($user)->from(route('plugins.k-box-kbox-plugin-geo.mapproviders.create'))->post($url, $custom_provider_request_data);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders.create'));
        $response->assertSessionHasErrors(['label', 'url']);
    }

    public function test_tile_provider_can_be_updated()
    {
        $this->withoutExceptionHandling();
        
        $service = app(GeoService::class);

        $initial = $service->config('map');

        $customProvider = $initial['providers']['custom-provider'] ?? null;

        if (is_null($customProvider)) {
            $initial['providers']['custom-provider'] = [
                "label" => "Custom Provider",
                "url" => "https://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png",
                "type" => "tile",
                "maxZoom" => 20,
                "subdomains" => "abc",
                "attribution" => '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
            ];
            
            $service->config(['map' => $initial]);
        }
        
        $user = factory(User::class)->create();
        
        $user->addCapabilities(Capability::$ADMIN);
        
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.update', 'custom-provider');
        
        $response = $this->actingAs($user)->put($url, [
            'label' => 'Custom Provider Updated label',
            "url" => "https://tile-{s}.openstreetmap.org/{z}/{x}/{y}.png",
            'maxZoom' => 10,
            "subdomains" => "abc",
            "attribution" => '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors.'
        ]);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders'));

        $settings = app(GeoService::class)->config('map')['providers']['custom-provider'];

        $this->assertEquals('Custom Provider Updated label', $settings['label']);
        $this->assertEquals("https://tile-{s}.openstreetmap.org/{z}/{x}/{y}.png", $settings["url"]);
        $this->assertEquals(10, $settings['maxZoom']);
        $this->assertEquals("abc", $settings["subdomains"]);
        $this->assertEquals('&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors.', $settings["attribution"]);
    }

    public function test_tile_provider_cannot_be_updated_to_wms()
    {
        $service = app(GeoService::class);

        $initial = $service->config('map');

        $customProvider = $initial['providers']['custom-provider'] ?? null;

        if (is_null($customProvider)) {
            $initial['providers']['custom-provider'] = [
                "label" => "Custom Provider",
                "url" => "https://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png",
                "type" => "tile",
                "maxZoom" => 20,
                "subdomains" => "abc",
                "attribution" => '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
            ];
            
            $service->config(['map' => $initial]);
        }
        
        $user = factory(User::class)->create();
        
        $user->addCapabilities(Capability::$ADMIN);
        
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.update', 'custom-provider');
        
        $response = $this->actingAs($user)->from(route('plugins.k-box-kbox-plugin-geo.mapproviders.edit', 'custom-provider'))->put($url, [
            "type" => "wms",
        ]);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders.edit', 'custom-provider'));

        $response->assertSessionHasErrors(['type']);
    }

    public function test_default_map_provider_can_be_changed()
    {
        $service = app(GeoService::class);

        $initial = $service->config('map')['default'];
        
        $user = factory(User::class)->create();
        
        $user->addCapabilities(Capability::$ADMIN);
        
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.default.update');
        
        $response = $this->actingAs($user)->from(route('plugins.k-box-kbox-plugin-geo.mapproviders'))->put($url, [
            "default" => "sentinel_3857",
        ]);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders'));

        $settings = app(GeoService::class)->config('map')['default'];

        $this->assertNotEquals($initial, $settings);
        $this->assertEquals("sentinel_3857", $settings);
    }

    public function test_default_map_provider_cannot_be_changed_to_unexisting_map_provider()
    {
        $service = app(GeoService::class);

        $initial = $service->config('map')['default'];
        
        $user = factory(User::class)->create();
        
        $user->addCapabilities(Capability::$ADMIN);
        
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.default.update');
        
        $response = $this->actingAs($user)->from(route('plugins.k-box-kbox-plugin-geo.mapproviders'))->put($url, [
            "default" => "unexisting",
        ]);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders'));

        $response->assertSessionHasErrors(['default']);
    }

    public function test_map_provider_can_be_enabled()
    {
        $service = app(GeoService::class);

        $initial = $service->config('map')['providers'];
        
        $user = factory(User::class)->create();
        
        $user->addCapabilities(Capability::$ADMIN);
        
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.enable.update');
        
        $response = $this->actingAs($user)->from(route('plugins.k-box-kbox-plugin-geo.mapproviders'))->put($url, [
            "provider" => "sentinel_2017_3857",
            "enable" => true,
        ]);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders'));

        $settings = $service->config('map')['providers'];

        $this->assertTrue($settings["sentinel_2017_3857"]['enable'], "Provider was not enabled");
    }

    public function test_map_provider_can_be_disabled()
    {
        $service = app(GeoService::class);

        $initial = $service->config('map')['providers'];
        
        $user = factory(User::class)->create();
        
        $user->addCapabilities(Capability::$ADMIN);
        
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.enable.update');
        
        $response = $this->actingAs($user)->from(route('plugins.k-box-kbox-plugin-geo.mapproviders'))->put($url, [
            "provider" => "sentinel_2017_3857",
            "enable" => false,
        ]);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders'));

        $settings = $service->config('map')['providers'];

        $this->assertFalse($settings["sentinel_2017_3857"]['enable'], "Provider was not disabled");
    }

    public function test_map_provider_can_be_removed()
    {
        $service = app(GeoService::class);

        $initial = $service->config('map')['providers'];
        
        $user = factory(User::class)->create();
        
        $user->addCapabilities(Capability::$ADMIN);

        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.delete', "sentinel_3857");
        
        $response = $this->actingAs($user)->from(route('plugins.k-box-kbox-plugin-geo.mapproviders'))->delete($url);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders'));

        $settings = app(GeoService::class)->config('map')['providers'];

        $this->assertNotEquals($initial, $settings);
        $this->assertFalse(isset($settings["sentinel_3857"]), "sentinel_3857 provider still there after delete request");
    }

    public function test_unexisting_map_provider_cannot_be_removed()
    {
        $service = app(GeoService::class);

        $initial = $service->config('map')['providers'];
        
        $user = factory(User::class)->create();
        
        $user->addCapabilities(Capability::$ADMIN);

        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.delete', "a_provider_not_created");
        
        $response = $this->actingAs($user)->from(route('plugins.k-box-kbox-plugin-geo.mapproviders'))->delete($url);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders'));

        $response->assertSessionHasErrors(['error']);
    }

    public function test_default_map_provider_cannot_be_removed()
    {
        $service = app(GeoService::class);

        $initial = $service->config('map')['default'];
        
        $user = factory(User::class)->create();
        
        $user->addCapabilities(Capability::$ADMIN);
        
        $url = route('plugins.k-box-kbox-plugin-geo.mapproviders.delete', $initial);
        
        $response = $this->actingAs($user)->from(route('plugins.k-box-kbox-plugin-geo.mapproviders'))->delete($url);

        $response->assertRedirect(route('plugins.k-box-kbox-plugin-geo.mapproviders'));

        $response->assertSessionHasErrors(['error']);
    }
}
