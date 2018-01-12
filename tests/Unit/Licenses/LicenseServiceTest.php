<?php

namespace Tests\Unit\Licenses;

use Tests\TestCase;
use OneOffTech\Licenses\License;
use Illuminate\Support\Collection;
use OneOffTech\Licenses\Services\LicenseService;
use OneOffTech\Licenses\Contracts\LicenseRepository;
use OneOffTech\Licenses\Exceptions\LicenseNotFoundException;
use OneOffTech\Licenses\Exceptions\LicensesLoadingException;

class LicenseServiceTest extends TestCase
{
    public function test_license_service_is_build()
    {
        $service = app()->make(LicenseRepository::class);

        $this->assertInstanceOf(LicenseRepository::class, $service);
        $this->assertInstanceOf(LicenseService::class, $service);
    }

    public function test_license_is_returned_if_found()
    {
        $service = app()->make(LicenseRepository::class);
        
        $license = $service->find('cc-by-4.0');
        
        $this->assertNotNull($license);
        $this->assertInstanceOf(License::class, $license);
    }
    
    public function test_find_or_fail_throws_exception_if_license_is_not_found()
    {
        $service = app()->make(LicenseRepository::class);

        $this->expectException(LicenseNotFoundException::class);
        
        $license = $service->findOrFail('mit');
    }

    public function test_license_loading_error_is_thrown_if_json_is_invalid()
    {
        config(['licenses.assets' => __DIR__]);

        $this->expectException(LicensesLoadingException::class);

        $service = app()->make(LicenseRepository::class);
    }

    public function test_all_licenses_are_returned()
    {
        $service = app()->make(LicenseRepository::class);

        $licenses = $service->all();

        $this->assertInstanceOf(Collection::class, $licenses);
        $this->assertContainsOnlyInstancesOf(License::class, $licenses);
        $this->assertEquals(8, $licenses->count());
    }
}
