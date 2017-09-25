<?php

namespace KlinkDMS\Listeners;

use Log;
use Exception;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Events\UploadCompleted;
use KlinkDMS\Jobs\ElaborateDocument;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UploadCompletedHandler implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  UploadCompleted  $event
     * @return void
     */
    public function handle(UploadCompleted $event)
    {
        Log::info("Upload completed for document {$event->descriptor->uuid}.");
        
        try {

            // make sure file upload is marked completed and descriptor has the processing status
            $descriptor = $this->updateDescriptor($event->descriptor);

            dispatch(new ElaborateDocument($descriptor));
        } catch (Exception $ex) {
            Log::error('Error while handling the UploadCompletedEvent.', ['event' => $event,'error' => $ex]);
            $event->descriptor->status = DocumentDescriptor::STATUS_ERROR;
            $event->descriptor->last_error = $ex;
            $event->descriptor->save();
        }
    }

    private function updateDescriptor($descriptor)
    {
        $file = $descriptor->file;
            
        $file->upload_completed_at = \Carbon\Carbon::now();
        
        $file->save();
        
        $descriptor->status = DocumentDescriptor::STATUS_PROCESSING;
        
        $descriptor->save();
        
        return $descriptor;
    }
}
