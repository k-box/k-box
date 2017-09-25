<?php

namespace KlinkDMS\DocumentsElaboration\Actions;

use KlinkDMS\Jobs\ThumbnailGenerationJob;
use KlinkDMS\Contracts\Action;

class GenerateThumbnail extends Action
{
    protected $canFail = true;

    public function run($descriptor)
    {
        dispatch(new ThumbnailGenerationJob($descriptor->file));
        
        return $descriptor;
    }
}
