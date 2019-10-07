<?php

namespace KBox\Listeners;

use Log;
use Exception;
use KBox\File;
use Carbon\Carbon;
use KBox\DuplicateDocument;
use KBox\DocumentDescriptor;
use KBox\Events\UploadCompleted;
use KBox\Events\FileDuplicateFoundEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use KBox\Jobs\CalculateUserUsedQuota;

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

            $this->duplicateCheck($descriptor, $event->user);

            // send the document descriptor
            // to the elaboration queue
            elaborate($descriptor);

            CalculateUserUsedQuota::dispatchNow($event->user);
        } catch (Exception $ex) {
            Log::error('Error while handling the UploadCompletedEvent.', ['event' => $event,'error' => $ex]);
            $event->descriptor->status = DocumentDescriptor::STATUS_ERROR;
            $event->descriptor->last_error = $ex;
            $event->descriptor->save();
        }
    }

    /**
     * Update the status on the descriptor and make sure
     * the linked file has the upload_completed_at date set
     */
    private function updateDescriptor($descriptor)
    {
        $file = $descriptor->file;
            
        $file->upload_completed_at = Carbon::now();
        
        $file->save();
        
        $descriptor->status = DocumentDescriptor::STATUS_PROCESSING;
        
        $descriptor->save();
        
        return $descriptor;
    }

    /**
     * Verify and signals if the uploaded document is a duplicate
     *
     * @return boolean true if duplicates are found
     */
    private function duplicateCheck($descriptor, $user)
    {
        $existings = File::withTrashed()->where('hash', $descriptor->file->hash)->whereHas('document')->with('document')->get();

        Log::info("Checking duplicates for $descriptor->hash", ['duplicates' => $existings]);

        if ($existings->isEmpty()) {
            return false;
        }

        $existings->each(function ($existing) use ($user, $descriptor) {
            if ($existing->document && $existing->document->id !== $descriptor->id && $existing->document->isAccessibleBy($user)) {
                $duplicate = DuplicateDocument::create([
                    'user_id' => $user->id,
                    'document_id' => $existing->document->id,
                    'duplicate_document_id' => $descriptor->id,
                ]);
                
                Log::info('Duplicate found', ['duplicate' => $duplicate]);

                $event = (new FileDuplicateFoundEvent($user, $duplicate))->delay(Carbon::now()->addMinutes(30));

                event($event);
            }
        });
    }
}
