<?php

namespace Tests\Unit\Commands;

use Artisan;
use Carbon\Carbon;
use KBox\File;
use Tests\TestCase;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\DB;
use KBox\User;
use OneOffTech\TusUpload\TusUpload;

class ClearCancelledDocumentUploadsCommandTest extends TestCase
{
    private function generateCancelledUploads($count = 3)
    {
        $user = User::factory()->create();
        
        $uploads = [];

        $upload_token_expiration = Carbon::now()->addHour();

        for ($i=0; $i < $count; $i++) {
            $upload = TusUpload::forceCreate([
                'request_id' => $i,
                'tus_id' => "tus-$i",
                'user_id' => $user->id,
                'filename' => "file-$i.txt",
                'size' => 10,
                'offset' => 4,
                'mimetype' => 'text/plain',
                'upload_token' => "upload-token-$i",
                'upload_token_expires_at' => $upload_token_expiration,
            ]);
            $upload->cancelled = true;
            $upload->save();

            $file = File::forceCreate([
                'name' => $upload->filename,
                'hash' => "this-is-an-hash-$i",
                'mime_type' => $upload->mimetype,
                'size' => $upload->size,
                'thumbnail_path' => null,
                'path' => '',
                'user_id' => $user->id,
                'original_uri' => '',
                'is_folder' => false,
                'request_id' => $i
            ]);

            $document = DocumentDescriptor::forceCreate([
                'local_document_id' => $i,
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
                'status' => DocumentDescriptor::STATUS_UPLOAD_CANCELLED
            ]);

            $uploads[] = $document;
        }

        return collect($uploads);
    }

    public function test_cancelled_uploads_are_removed()
    {
        // \Schema::disableForeignKeyConstraints();
        // DB::table('document_descriptors')->truncate();
        // DB::table('files')->truncate();
        $previous_documents = DocumentDescriptor::withTrashed()->count();
        $previous_files = File::withTrashed()->count();
        DB::table('tus_uploads_queue')->truncate();

        $doc_that_should_remain = DocumentDescriptor::factory()->create();
        $uploads = $this->generateCancelledUploads(3);

        $exitCode = Artisan::call('documents:clear-cancelled', []);

        $this->assertEquals(0, $exitCode);
        $this->assertEquals(0, TusUpload::count());
        $this->assertEquals($previous_documents + 1, DocumentDescriptor::withTrashed()->count());
    }
}
