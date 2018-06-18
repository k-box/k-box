<?php

namespace KBox\Listeners;

use OneOffTech\TusUpload\Events\TusUploadCancelled;
use KBox\File;
use KBox\DocumentDescriptor;
use Log;

class TusUploadCancelledHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  TusUploadCompleted  $event
     * @return void
     */
    public function handle(TusUploadCancelled $event)
    {
        Log::info("Upload {$event->upload->request_id} cancelled.");

        try {

            //todo: do changes in transaction, if one fails, the other should not be updated
            $file = File::where('request_id', $event->upload->request_id)->first();

            if (is_null($file)) {
                throw new \Exception("File for upload {$event->upload->request_id} not found");
            }

            $descriptor = $this->updateDescriptor($file, $event);
        } catch (\Exception $ex) {
            Log::error('File move or descriptor update error while handling the TusUploadCancelled event.', ['upload' => $event->upload,'error' => $ex]);
        }
    }

    private function updateDescriptor($file, $event)
    {
        $descriptor = $file->document;
        
        try {
            $file->upload_cancelled_at = \Carbon\Carbon::now();
            
            $file->save();
            
            $descriptor->status = DocumentDescriptor::STATUS_UPLOAD_CANCELLED;
            
            $descriptor->save();
            
            return $descriptor;
        } catch (\Exception $ex) {
            Log::error('File move or descriptor update error while handling the TusUploadCancelled event.', ['upload' => $event->upload,'error' => $ex]);
            $descriptor->status = DocumentDescriptor::STATUS_ERROR;
            $descriptor->last_error = $ex;
            $descriptor->save();
        }
    }
}
