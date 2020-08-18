<?php

namespace Tests\Feature\Appearance;

use Illuminate\Support\Facades\Storage;
use KBox\Appearance\HeroPicture;
use KBox\Jobs\DownloadAppearancePicture;
use Tests\TestCase;

class DownloadAppearancePictureJobTest extends TestCase
{
    public function test_image_can_be_fetched()
    {
        Storage::fake('public');
        
        (new DownloadAppearancePicture(config('appearance.picture')))->handle();

        $name = hash('sha256', config('appearance.picture'));
        Storage::disk('public')->assertExists("appearance/$name.jpg");
    }
    
    public function test_image_fetch_honors_cache()
    {
        Storage::fake('public');
        
        $filename = (new HeroPicture(config('appearance.picture')))->name();
        Storage::disk('public')->put($filename, 'test');
        $sizeBefore = Storage::disk('public')->size($filename);
        
        (new DownloadAppearancePicture(config('appearance.picture')))->handle();

        $sizeAfter = Storage::disk('public')->size($filename);

        $this->assertEquals($sizeBefore, $sizeAfter);
    }
    
    public function test_image_fetch_can_be_forced()
    {
        Storage::fake('public');
        
        $filename = (new HeroPicture(config('appearance.picture')))->name();
        Storage::disk('public')->put($filename, 'test');
        $sizeBefore = Storage::disk('public')->size($filename);
        
        (new DownloadAppearancePicture(config('appearance.picture'), true))->handle();

        $sizeAfter = Storage::disk('public')->size($filename);

        $this->assertNotEquals($sizeBefore, $sizeAfter);
    }
}
