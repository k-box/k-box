<?php

namespace KBox\Geo\Listeners;

use Log;
use Exception;
use KBox\Geo\GeoService;
use KBox\Events\FileDeleting;
use OneOffTech\GeoServer\GeoFile;
use Illuminate\Queue\InteractsWithQueue;
use KBox\Geo\Jobs\RemoveStoreFromGeoserver;
use Illuminate\Contracts\Queue\ShouldQueue;
use OneOffTech\GeoServer\Exception\GeoServerClientException;

class RemoveFileFromGeoserver
{
    /**
     * @var \KBox\Geo\GeoService
     */
    private $service = null;
     
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(GeoService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  FileDeleting  $event
     * @return void
     */
    public function handle(FileDeleting $event)
    {
        if(! $event->forceDeleted){
            return;
        }

        if($this->service->isEnabled() && GeoFile::isSupported($event->file->absolute_path)){
            $data = GeoFile::from($event->file->absolute_path)->name($event->file->uuid);

            Log::info("Dispatching store removal from Geoserver for {$event->file->uuid}", compact('data'));

            dispatch(new RemoveStoreFromGeoserver($data->name, $data->type));
        }
    }
}
