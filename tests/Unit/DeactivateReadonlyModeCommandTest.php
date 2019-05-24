<?php

namespace Tests\Unit;

use Mockery as m;
use Tests\TestCase;
use KBox\Services\ReadonlyMode;
use Illuminate\Filesystem\Filesystem;

class DeactivateReadonlyModeCommandTest extends TestCase
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

    public function test_deactivate_readonly_mode()
    {
        $service = $this->createReadonlyModeService();

        $this->artisan("readonly:down")
            ->expectsOutput('Application is now live.')
            ->assertExitCode(0);

        $this->assertFileNotExists($this->readonlyFilePath);
        $this->assertFalse($service->isReadonlyActive());
    }

    protected function createReadonlyModeService()
    {
        $this->makeReadonlyFile();

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
