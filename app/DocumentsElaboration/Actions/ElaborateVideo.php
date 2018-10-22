<?php

namespace KBox\DocumentsElaboration\Actions;

use Log;
use KBox\Jobs\ConvertVideo;
use KBox\Contracts\Action;
use OneOffTech\VideoProcessing\VideoProcessorFactory;

class ElaborateVideo extends Action
{
    protected $canFail = true;

    public function run($descriptor)
    {
        $file = $descriptor->file;
        if (VideoProcessorFactory::isInstalled() && $file->isVideo() && $file->size <= (200 * 1024 * 1024)) {
            Log::info("Elaborate video action queued for $descriptor->uuid");
            dispatch(new ConvertVideo($descriptor));
        }
        
        return $descriptor;
    }
}
