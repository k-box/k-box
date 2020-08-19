<?php

namespace Tests\Feature\Appearance;

use Tests\TestCase;

class AppearanceColorTest extends TestCase
{
    public function test_default_color_used()
    {
        config([
            'appearance.picture' => null,
            'appearance.color' => null,
        ]);

        $response = $this->get(route('login'));

        $response->assertSee('bg-gray-900');
        $response->assertDontSee('background-color:');
        $response->assertDontSee('object-cover');
    }

    public function test_color_is_honored()
    {
        config([
            'appearance.picture' => null,
            'appearance.color' => '#00ff00',
        ]);

        $response = $this->get(route('login'));

        $response->assertSee('background-color: #00ff00');
        $response->assertDontSee('object-cover');
    }

    public function test_color_applied_only_if_valid_hex_value()
    {
        config([
            'appearance.picture' => null,
            'appearance.color' => 'blue',
        ]);

        $response = $this->get(route('login'));

        $response->assertDontSee('background-color');
    }
}
