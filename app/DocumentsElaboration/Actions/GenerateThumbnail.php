<?php

namespace KBox\DocumentsElaboration\Actions;

use KBox\Jobs\ThumbnailGenerationJob;
use KBox\Contracts\Action;

class GenerateThumbnail extends Action
{
    protected $canFail = true;

    public function run($descriptor)
    {
        dispatch_now(new ThumbnailGenerationJob($descriptor->file));
        
        return $descriptor;
    }
}
