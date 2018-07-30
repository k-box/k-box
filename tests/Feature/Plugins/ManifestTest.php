<?php

namespace Tests\Feature\Plugins;

use Tests\TestCase;
use KBox\Plugins\Manifest;

class ManifestTest extends TestCase
{
    public function test_default_values_are_appended()
    {
        $attributes = [
            'description' => 'Example K-Box plugin',
            'license' => 'MIT',
            'authors' => [[
                'name' => 'Alessio Vertemati',
                'email' => 'alessio@oneofftech.xyz',
            ]],
            'path' => 'kbox/plugins/example',
            'name' => 'k-box-example-plugin',
            'providers' => ['KBox\\Plugins\\Example\\ExamplePlugin']
        ];

        $manifest = new Manifest($attributes);
        
        $this->assertEquals(false, $manifest->enabled);
        $this->assertEquals([], $manifest->configuration);
        $this->assertEquals(array_merge($attributes, ['enabled' => false, 'configuration' => []]), $manifest->toArray());
    }

    public function test_default_do_not_override_values()
    {
        $attributes = [
            'description' => 'Example K-Box plugin',
            'license' => 'MIT',
            'authors' => [[
                'name' => 'Alessio Vertemati',
                'email' => 'alessio@oneofftech.xyz',
            ]],
            'path' => 'kbox/plugins/example',
            'name' => 'k-box-example-plugin',
            'providers' => ['KBox\\Plugins\\Example\\ExamplePlugin'],
            'enabled' => true,
            'configuration' => [],
        ];

        $manifest = new Manifest($attributes);
        
        $this->assertEquals($attributes, $manifest->toArray());
    }
}
