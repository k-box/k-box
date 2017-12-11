<?php

namespace KBox\Listeners;

use Log;
use KBox\File;
use KBox\DocumentDescriptor;
use KBox\Events\UploadCompleted;
use Illuminate\Support\Facades\Storage;
use Klink\DmsDocuments\DocumentsService;
use Avvertix\TusUpload\Events\TusUploadCompleted;
use Klink\DmsAdapter\KlinkDocumentUtils;

class TusUploadCompletedHandler
{
    /**
     * @var \Klink\DmsDocuments\DocumentsService
     */
    private $documentsService = null;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DocumentsService $documentsService)
    {
        $this->documentsService = $documentsService;
    }

    /**
     * Handle the event.
     *
     * @param  TusUploadCompleted  $event
     * @return void
     */
    public function handle(TusUploadCompleted $event)
    {
        Log::info("Upload {$event->upload->request_id} completed.");

        try {
            $file = File::where('request_id', $event->upload->request_id)->first();

            if (is_null($file)) {
                throw new \Exception("File for upload {$event->upload->request_id} not found");
            }

            $descriptor = $this->updateDescriptor($file, $event);

            $descriptor = $descriptor->fresh();
            
            event(new UploadCompleted($descriptor, $descriptor->owner));
        } catch (\Exception $ex) {
            Log::error('File move or descriptor update error while handling the TusUploadCompleted event.', ['upload' => $event->upload,'error' => $ex]);
        }
    }

    private function updateDescriptor($file, $event)
    {
        $descriptor = $file->document;
        
        try {
            $extension = pathinfo($file->name, PATHINFO_EXTENSION);

            if (empty($extension)) {
                $extension = KlinkDocumentUtils::getExtensionFromMimeType($file->mime_type);
            }

            $storage = Storage::disk('local');

            $filename = $file->uuid.'.'.$extension;

            $destination_path = date('Y').'/'.date('m').'/'.$file->uuid.'/';

            $file_path = $destination_path.$filename;
            
            $storage->makeDirectory($destination_path);

            $destination = $storage->path($file_path);

            // move the file to the new location
            
            rename($event->upload->path(), $destination);

            $file->path = $file_path;

            $file->hash = KlinkDocumentUtils::generateDocumentHash($destination);
            
            $file->upload_completed_at = \Carbon\Carbon::now();
            
            $file->save();

            $descriptor->hash = $file->hash;
            
            $descriptor->status = DocumentDescriptor::STATUS_UPLOAD_COMPLETED;
            
            $descriptor->save();
            
            return $descriptor;
        } catch (\Exception $ex) {
            Log::error('File move or descriptor update error while handling the TusUploadCompleted event.', ['upload' => $event->upload,'error' => $ex]);
            $descriptor->status = DocumentDescriptor::STATUS_ERROR;
            $descriptor->last_error = $ex;
            $descriptor->save();
            return $descriptor;
        }
    }
}
