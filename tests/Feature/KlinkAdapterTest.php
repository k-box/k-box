<?php

namespace Tests\Feature;

use Tests\TestCase;
use KlinkDMS\Option;
use Klink\DmsAdapter\KlinkAdapter;
use Klink\DmsAdapter\KlinkVisibilityType;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KlinkAdapterTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp()
    {
        if (empty(getenv('DMS_CORE_ADDRESS'))) {
            $this->markTestSkipped(
              'DMS_CORE_ADDRESS not configured for running integration tests.'
            );
        }

        parent::setUp();
    }

    
    public function test_adapter_report_succesfull_connection_test()
    {
        $test_results = KlinkAdapter::test(config('dms.core.address'));

        $this->assertNotNull($test_results);
        $this->assertTrue(is_array($test_results), 'test result is not an array');
        $this->assertEquals(['status' => 'ok' ], $test_results);
    }
    
    public function test_adapter_report_failed_connection_test()
    {
        $test_results = KlinkAdapter::test('http://localhost.local/');

        $this->assertNotNull($test_results);
        $this->assertTrue(is_array($test_results), 'test result is not an array');
        $this->assertArraySubset(['status' => 'error'], $test_results);
        $this->assertArrayHasKey('error', $test_results);
        $this->assertNotEmpty($test_results['error']);
    }
    
    public function test_adapter_check_private_connection()
    {
        $test_results = app('klinkadapter')->canConnect();

        $this->assertNotNull($test_results);
        $this->assertTrue(is_array($test_results), 'test result is not an array');
        $this->assertEquals(['status' => 'ok' ], $test_results);
    }
    
    public function test_adapter_check_private_connection_and_report_error()
    {
        config(['dms.core.address' => 'http://localhost.local/']);
        $test_results = app('klinkadapter')->canConnect();

        $this->assertNotNull($test_results);
        $this->assertTrue(is_array($test_results), 'test result is not an array');
        $this->assertArraySubset(['status' => 'error'], $test_results);
        $this->assertArrayHasKey('error', $test_results);
        $this->assertNotEmpty($test_results['error']);
    }
    
    public function test_adapter_check_public_connection()
    {
        // simulate a public connection using the same parameters as the local one
        Option::put(Option::PUBLIC_CORE_URL, config('dms.core.address'));
        Option::put(Option::PUBLIC_CORE_ENABLED, true);
        Option::put(Option::PUBLIC_CORE_CORRECT_CONFIG, true);
        Option::put(Option::PUBLIC_CORE_PASSWORD, '');

        $test_results = app('klinkadapter')->canConnect('public');

        $this->assertNotNull($test_results);
        $this->assertTrue(is_array($test_results), 'test result is not an array');
        $this->assertEquals(['status' => 'ok' ], $test_results);
    }
    
    public function test_adapter_check_public_connection_and_report_error()
    {
        Option::put(Option::PUBLIC_CORE_URL, 'http://localhost.local/');
        Option::put(Option::PUBLIC_CORE_ENABLED, true);
        Option::put(Option::PUBLIC_CORE_CORRECT_CONFIG, true);
        Option::put(Option::PUBLIC_CORE_PASSWORD, 'a-token');

        $test_results = app('klinkadapter')->canConnect('public');

        $this->assertNotNull($test_results);
        $this->assertTrue(is_array($test_results), 'test result is not an array');
        $this->assertArraySubset(['status' => 'error'], $test_results);
        $this->assertArrayHasKey('error', $test_results);
        $this->assertNotEmpty($test_results['error']);
    }

    public function test_adapter_returns_private_as_only_available_connection()
    {
        $test_results = app('klinkadapter')->availableConnections();

        $this->assertEquals([KlinkVisibilityType::KLINK_PRIVATE], $test_results);
    }
    
    public function test_adapter_returns_private_and_public_as_available_connections()
    {
        // simulate a public connection using the same parameters as the local one
        Option::put(Option::PUBLIC_CORE_URL, config('dms.core.address'));
        Option::put(Option::PUBLIC_CORE_ENABLED, true);
        Option::put(Option::PUBLIC_CORE_CORRECT_CONFIG, true);
        Option::put(Option::PUBLIC_CORE_PASSWORD, '');

        $test_results = app('klinkadapter')->availableConnections();

        $this->assertEquals([
            KlinkVisibilityType::KLINK_PRIVATE,
            KlinkVisibilityType::KLINK_PUBLIC,
        ], $test_results);
    }
}
