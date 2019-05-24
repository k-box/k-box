<?php

namespace Tests\Unit;

use Mockery as m;
use Tests\TestCase;
use Illuminate\Http\Request;
use KBox\Services\ReadonlyMode;
use Illuminate\Filesystem\Filesystem;
use KBox\Exceptions\ReadonlyModeException;
use KBox\Http\Middleware\CheckForReadonlyMode;

class ReadonlyModeMiddlewareTest extends TestCase
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

    public function test_application_is_running_normally()
    {
        $service = m::mock(ReadonlyMode::class);

        $service->shouldReceive('isReadonlyActive')->once()->andReturn(false);

        $middleware = new CheckForReadonlyMode($service);

        $result = $middleware->handle(Request::create('/', 'POST'), function ($request) {
            return 'Running normally.';
        });

        $this->assertSame('Running normally.', $result);
    }

    public function test_post_requests_are_blocked_in_readonly_mode()
    {
        $service = $this->createReadonlyModeService();

        $this->expectException(ReadonlyModeException::class);
        $this->expectExceptionMessage('This application is in readonly mode.');

        $middleware = new CheckForReadonlyMode($service);

        $result = $middleware->handle(Request::create('/', 'POST'), function ($request) {
        });
    }
    
    public function test_put_requests_are_blocked_in_readonly_mode()
    {
        $service = $this->createReadonlyModeService();

        $this->expectException(ReadonlyModeException::class);
        $this->expectExceptionMessage('This application is in readonly mode.');

        $middleware = new CheckForReadonlyMode($service);

        $result = $middleware->handle(Request::create('/', 'PUT'), function ($request) {
        });
    }
    
    public function test_delete_requests_are_blocked_in_readonly_mode()
    {
        $service = $this->createReadonlyModeService();

        $this->expectException(ReadonlyModeException::class);
        $this->expectExceptionMessage('This application is in readonly mode.');

        $middleware = new CheckForReadonlyMode($service);

        $result = $middleware->handle(Request::create('/', 'DELETE'), function ($request) {
        });
    }
    
    public function test_get_requests_are_not_blocked_in_readonly_mode()
    {
        $service = $this->createReadonlyModeService();

        $middleware = new CheckForReadonlyMode($service);

        $result = $middleware->handle(Request::create('/'), function ($request) {
            return 'Running normally.';
        });

        $this->assertSame('Running normally.', $result);
    }

    public function test_application_allows_some_URIs()
    {
        $service = $this->createReadonlyModeService();

        $middleware = new class($service) extends CheckForReadonlyMode {
            public function __construct($service)
            {
                parent::__construct($service);
                $this->except = ['foo/*'];
            }
        };

        $result = $middleware->handle(Request::create('/foo/bar', 'POST'), function ($request) {
            return 'Excepting /foo/bar';
        });

        $this->assertSame('Excepting /foo/bar', $result);
    }

    protected function createReadonlyModeService($ips = null)
    {
        $this->makeReadonlyFile($ips);

        $readonlyModeService = m::mock(ReadonlyMode::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        
        $readonlyModeService->shouldReceive('storagePath')->andReturn($this->storagePath);

        return $readonlyModeService;
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
