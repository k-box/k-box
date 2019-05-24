<?php

namespace Tests\Unit;

use Mockery as m;
use Tests\TestCase;
use KBox\Services\ReadonlyMode;
use Illuminate\Filesystem\Filesystem;

class ActivateReadonlyModeCommandTest extends TestCase
{

    /**
     * @var string
     */
    protected $storagePath;

    /**
     * @var string
     */
    protected $readonlyFilePath;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    
    protected function setUp(): void
    {
        parent::setUp();

        if (is_null($this->files)) {
            $this->files = new Filesystem;
        }

        $this->storagePath = __DIR__.'/tmp';

        $this->readonlyFilePath = $this->storagePath.'/framework/readonly';
        $this->files->makeDirectory($this->storagePath.'/framework', 0755, true);
    }

    protected function tearDown(): void
    {
        $this->files->deleteDirectory($this->storagePath);

        m::close();
    }

    public function test_activate_readonly_mode()
    {
        $service = $this->createReadonlyModeService();

        $this->artisan("readonly:up")
            ->expectsOutput('Application is now in readonly mode.')
            ->assertExitCode(0);

        $this->assertFileExists($this->readonlyFilePath);
        $this->assertTrue($service->isReadonlyActive());
        $config = $service->getReadonlyConfiguration();
        $this->assertArrayHasKey('time', $config);
        $this->assertArrayHasKey('message', $config);
        $this->assertArrayHasKey('retry', $config);
        $this->assertArrayHasKey('allowed', $config);
    }

    public function test_activate_readonly_mode_with_custom_message()
    {
        $service = $this->createReadonlyModeService();

        $this->artisan("readonly:up", [
                '--message' => 'A new message'
            ])
            ->expectsOutput('Application is now in readonly mode.')
            ->assertExitCode(0);
        
        $config = $service->getReadonlyConfiguration();
        $this->assertEquals('A new message', $config['message']);
    }

    public function test_activate_readonly_mode_with_custom_retry()
    {
        $service = $this->createReadonlyModeService();

        $this->artisan("readonly:up", [
                '--retry' => 600
            ])
            ->expectsOutput('Application is now in readonly mode.')
            ->assertExitCode(0);
        
        $config = $service->getReadonlyConfiguration();
        $this->assertEquals(600, $config['retry']);
    }

    public function test_activate_readonly_mode_with_custom_allow()
    {
        $service = $this->createReadonlyModeService();

        $this->artisan("readonly:up", [
                '--allow' => "127.0.0.1"
            ])
            ->expectsOutput('Application is now in readonly mode.')
            ->assertExitCode(0);
        
        $config = $service->getReadonlyConfiguration();
        $this->assertEquals("127.0.0.1", $config['allowed']);
    }
    
    protected function createReadonlyModeService()
    {
        $fake = m::mock(ReadonlyMode::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        
        $fake->shouldReceive('storagePath')->andReturn($this->storagePath);

        $this->swap(ReadonlyMode::class, $fake);

        return $fake;
    }

    /**
     * Make a readonly file with the given allowed ips.
     *
     * @param  string|array  $ips
     * @return array
     */
    protected function makeReadonlyFile($ips = null)
    {
        $data = [
            'time' => time(),
            'retry' => 86400,
            'message' => 'This application is in readonly mode.',
        ];

        if ($ips !== null) {
            $data['allowed'] = $ips;
        }

        $this->files->put($this->readonlyFilePath, json_encode($data, JSON_PRETTY_PRINT));

        return $data;
    }
}
