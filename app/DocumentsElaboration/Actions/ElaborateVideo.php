<?php

namespace KlinkDMS\DocumentsElaboration\Actions;

use KlinkDMS\Jobs\ConvertVideo;
use KlinkDMS\Contracts\Action;
use OneOffTech\VideoProcessing\VideoProcessorFactory;

class ElaborateVideo extends Action
{
    protected $canFail = true;

    public function run($descriptor)
    {
        if (VideoProcessorFactory::isInstalled()) {
            \Log::info("Elaborate video action queued for $descriptor->uuid");
            dispatch(new ConvertVideo($descriptor));
        }
        
        return $descriptor;
    }
}
