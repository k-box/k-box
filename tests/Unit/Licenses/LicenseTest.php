<?php

namespace Tests\Unit\Licenses;

use Tests\TestCase;
use OneOffTech\Licenses\License;
use OneOffTech\Licenses\Contracts\LicenseRepository;

class LicenseTest extends TestCase
{
    public function test_license_details_are_loaded()
    {
        $service = app()->make(LicenseRepository::class);
        
        $license = $service->find('cc-by-4.0');
        
        $this->assertNotNull($license);
        $this->assertInstanceOf(License::class, $license);
        $this->assertEquals('CC-BY-4.0', $license->id);
        $this->assertEquals('CC BY 4.0', $license->shortTitle);
        $this->assertEquals('CC BY 4.0', $license->short_title);
        $this->assertEquals('Creative Commons Attribution 4.0', $license->name);
        $this->assertEquals("Creative Commons Attribution 4.0 International", $license->title);
        $this->assertNotEmpty($license->description);
        $this->assertStringStartsWith('#### You are free to:', $license->description);
        $this->assertStringStartsWith('<h4>You are free to:', $license->html_description);
        $this->assertNotEmpty($license->icon);
        $this->assertFileExists($license->description_path);
        $this->assertFileExists($license->icon_path);
        $this->assertEquals('http://creativecommons.org/licenses/by/4.0/legalcode', $license->license);
    }
    
    public function test_null_license_url_is_handled()
    {
        $service = app()->make(LicenseRepository::class);
        
        $license = $service->find('C');
        
        $this->assertNotNull($license);
        $this->assertInstanceOf(License::class, $license);
        $this->assertNull($license->license);
    }
}
