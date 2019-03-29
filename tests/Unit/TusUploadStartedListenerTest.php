<?php

namespace Tests\Unit;

use KBox\User;
use KBox\File;
use Carbon\Carbon;
use KBox\GroupType;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;
use OneOffTech\TusUpload\TusUpload;
use KBox\Listeners\TusUploadStartedHandler;
use OneOffTech\TusUpload\Events\TusUploadStarted;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TusUploadStartedListenerTest extends TestCase
{
    use DatabaseTransactions;

    private function createEvent($userId = 1, $requestId = '14b1c4c77771671a8479bc0444bbc5ce', $collection = null)
    {
        $upload = TusUpload::forceCreate([
            'request_id' => $requestId,
            'user_id' => $userId,
            'filename' => 'test.png',
            'size' => 46205,
            'offset' => 0,
            'upload_token' => 'AAAAAAAAAAAA',
            'upload_token_expires_at' => Carbon::now()->addHour(),
            'metadata' => ['collection' => $collection]
        ]);

        return new TusUploadStarted($upload);
    }

    public function test_document_descriptor_is_created()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

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
    
    public function test_document_descriptor_is_created_in_collection()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $request_id = 'REQUEST';

        $collection = $user->groups()->create([
            'name' => 'That exact collection',
            'is_private' => true,
            'color' => '16a085',
            'group_type_id' => GroupType::getGenericType()->id,
        ]);

        $started_event = $this->createEvent($user->id, $request_id, $collection->id);

        $listener = app(TusUploadStartedHandler::class);

        $listener->handle($started_event);

        $file = File::where('request_id', $request_id)->first();

        $this->assertNotNull($file);
        $this->assertTrue($file->upload_started);
        $this->assertNotNull($file->document);
        $uploaded_descriptor = $file->document;
        $this->assertEquals(DocumentDescriptor::STATUS_UPLOADING, $uploaded_descriptor->status);
        $this->assertEquals(1, $uploaded_descriptor->groups()->count());
        $this->assertEquals('That exact collection', $uploaded_descriptor->groups->first()->name);
    }
}
