<?php

namespace Tests\Feature;

use Tests\TestCase;
use KBox\File;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileDirectDownloadTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    public function test_file_downloaded_forbidden_without_token()
    {
        $file = factory(File::class)->create();

        $response = $this->get('/files/'.$file->uuid);

        $response->assertStatus(403);
    }

    public function test_file_downloaded_forbidden_with_invalid_token()
    {
        $file = factory(File::class)->create();

        $response = $this->get('/files/'.$file->uuid.'?t=hello');

        $response->assertStatus(403);
    }

    public function test_file_can_be_downloaded_given_uuid()
    {
        $file = factory(File::class)->create();

        // generate a link to it with the temporary token

        $token = $file->generateDownloadToken();

        // invoke the download route

        $response = $this->get('/files/'.$file->uuid.'?t='.$token);

        $response->assertStatus(200);

        // expects a file download response

        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
        
        $sent_file = $response->getFile();

        $this->assertEquals($file->absolute_path, $sent_file->getPathname());
        $this->assertEquals($file->mime_type, $sent_file->getMimeType());
    }
}
