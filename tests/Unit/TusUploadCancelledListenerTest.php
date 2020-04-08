<?php

namespace Tests\Unit;

use KBox\User;
use KBox\File;
use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Support\Str;
use KBox\DocumentDescriptor;
use OneOffTech\TusUpload\TusUpload;
use KBox\Listeners\TusUploadCancelledHandler;
use OneOffTech\TusUpload\Events\TusUploadCancelled;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TusUploadCancelledListenerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        config([
            'tusupload.storage' => storage_path('')
        ]);
    }

    private function createEvent($user, $requestId = '14b1c4c77771671a8479bc0444bbc5ce')
    {
        $path = storage_path(Str::slug($requestId).'.bin');

        file_put_contents($path, 'Test File Content');

        $size = filesize($path);

        $upload = TusUpload::forceCreate([
            'request_id' => $requestId,
            'tus_id' => Str::slug($requestId),
            'user_id' => $user->id,
            'filename' => basename($path).'.txt',
            'size' => $size,
            'offset' => $size,
            'mimetype' => 'text/plain',
            'upload_token' => 'AAAAAAAAAAAA',
            'upload_token_expires_at' => Carbon::now()->addHour(),
        ]);

        $upload->cancelled = true;
        $upload->save();
        
        $file = File::forceCreate([
            'name' => $upload->filename,
            'hash' => hash_file('sha512', $path),
            'mime_type' => $upload->mimetype,
            'size' => $upload->size,
            'thumbnail_path' => null,
            'path' => '',
            'user_id' => $user->id,
            'original_uri' => '',
            'is_folder' => false,
            'request_id' => $requestId
        ]);

        $descr = DocumentDescriptor::forceCreate([
            'institution_id' => null,
            'local_document_id' => substr($file->hash, 0, 6),
            'title' => $file->name,
            'hash' => $file->hash,
            'document_uri' => 'https://something.com',
            'thumbnail_uri' => 'https://something.com',
            'mime_type' => $file->mime_type,
            'visibility' => 'private',
            'document_type' => 'document',
            'user_owner' => $user->name.' <'.$user->email.'>',
            'user_uploader' => $user->name.' <'.$user->email.'>',
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'created_at' => $file->created_at,
            'status' => DocumentDescriptor::STATUS_UPLOADING
        ]);

        return new TusUploadCancelled($upload->fresh());
    }

    public function test_document_descriptor_is_updated()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $request_id = 'REQUEST';

        $cancelled_event = $this->createEvent($user, $request_id);

        $listener = app(TusUploadCancelledHandler::class);

        $listener->handle($cancelled_event);

        $file = File::where('request_id', $request_id)->first();

        $this->assertNotNull($file);
        $this->assertNotNull($file->upload_cancelled_at);
        $this->assertTrue($file->upload_cancelled);
        $this->assertNotNull($file->document);
        
        $document = $file->document;
        $this->assertEquals(DocumentDescriptor::STATUS_UPLOAD_CANCELLED, $document->status);
    }
}
