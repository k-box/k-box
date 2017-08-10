<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Avvertix\TusUpload\Events\TusUploadStarted;
use Avvertix\TusUpload\TusUpload;
use KlinkDMS\Listeners\TusUploadStartedHandler;
use Carbon\Carbon;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\File;

class TusUploadStartedListenerTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    private function createEvent($userId = 1, $requestId = '14b1c4c77771671a8479bc0444bbc5ce')
    {
        $upload = TusUpload::forceCreate([
            'request_id' => $requestId,
            'user_id' => $userId,
            'filename' => 'test.png',
            'size' => 46205,
            'offset' => 0,
            'upload_token' => 'AAAAAAAAAAAA',
            'upload_token_expires_at' => Carbon::now()->addHour()
        ]);

        return new TusUploadStarted($upload);
    }

    public function test_document_descriptor_is_created()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createAdminUser();

        $request_id = 'REQUEST';

        $started_event = $this->createEvent($user->id, $request_id);

        $listener = app(TusUploadStartedHandler::class);

        $listener->handle($started_event);

        $file = File::where('request_id', $request_id)->first();

        $this->assertNotNull($file);
        $this->assertTrue($file->upload_started);
        $this->assertNotNull($file->document);
        $this->assertEquals(DocumentDescriptor::STATUS_UPLOADING, $file->document->status);
    }
}
