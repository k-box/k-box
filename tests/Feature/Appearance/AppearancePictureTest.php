<?php

namespace Tests\Feature\Appearance;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AppearancePictureTest extends TestCase
{
    public function test_external_image_url()
    {
        Storage::fake('public');

        $pictureUrl = 'https://images.unsplash.com/photo-1563654727148-d7e9d1ed2a86?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80';
        $hash = hash('sha256', $pictureUrl);

        Storage::disk('public')->makeDirectory('appearance');
        Storage::disk('public')->put("appearance/$hash.jpg", file_get_contents(public_path('images/land-medium.jpg')));

        config([
            'appearance.picture' => $pictureUrl,
            'appearance.color' => null,
        ]);

        $response = $this->get(route('login'));

        $response->assertSee('bg-gray-900');
        $response->assertSee('object-cover');
        $pictureExpectedUrl = 'storage/appearance/'.$hash.'.jpg';
        $response->assertSee(url($pictureExpectedUrl));
    }

    public function test_local_image_url()
    {
        $pictureUrl = 'http://localhost/images/land-large.jpg';
        config([
            'appearance.picture' => $pictureUrl,
            'appearance.color' => null,
        ]);

        $response = $this->get(route('login'));

        $response->assertSee('bg-gray-900');
        $response->assertSee('object-cover');
        $response->assertSee(url('images/land-large.jpg'));
    }

    public function test_relative_local_image_url()
    {
        $pictureUrl = 'images/land-large.jpg';
        config([
            'appearance.picture' => $pictureUrl,
            'appearance.color' => null,
        ]);

        $response = $this->get(route('login'));

        $response->assertSee('bg-gray-900');
        $response->assertSee('object-cover');
        $response->assertSee(url($pictureUrl));
    }
}
