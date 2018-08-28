<?php

namespace KBox\Listeners;

use Log;
use KBox\Group;
use KBox\Documents\Services\DocumentsService;
use KBox\Documents\Facades\Files;
use KBox\Documents\FileHelper;
use OneOffTech\TusUpload\Events\TusUploadStarted;

class TusUploadStartedHandler
{
    /**
     *
     *
     * @var \KBox\Documents\Services\DocumentsService
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
     * @param  TusUploadStarted  $event
     * @return void
     */
    public function handle(TusUploadStarted $event)
    {
        Log::info("Upload {$event->upload->request_id} started.");
        
        try {
            list($recognizedMimeType) = Files::recognize($event->upload->filename);
            $mime = $event->upload->mimetype ? FileHelper::normalizeMimeType($event->upload->mimetype) : $recognizedMimeType;

            // creating the File entry

            $file = $this->documentsService->createFile(
                $event->upload->user_id,
                $event->upload->filename,
                $mime,
                '',
                hash('sha512', $event->upload->request_id.$event->upload->user_id.$event->upload->filename), // used as the hash value as the file is not yet here
                $event->upload->size
            );

            // links the newly created File to the upload job
            $file->request_id = $event->upload->request_id;
            $file->upload_started = true;

            $file->save();

            // Creating the correspondent DocumentDescriptor
            $collection = null;

            if ($event->upload->metadata && isset($event->upload->metadata['collection'])) {
                $collection = Group::find($event->upload->metadata['collection']);
            }

            $descriptor = $this->documentsService->createDocumentDescriptor($file->fresh(), 'private', $collection);
        } catch (\Exception $ex) {
            Log::error('File creation error while handling the TusUploadStarted event.', ['upload' => $event->upload,'error' => $ex]);

            // not throwing the exception outside as this is executed asynchronously
        }
    }
}
