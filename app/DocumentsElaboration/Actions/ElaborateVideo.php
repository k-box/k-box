<?php

namespace KlinkDMS\DocumentsElaboration\Actions;

use KlinkDMS\Jobs\ConvertVideo;
use KlinkDMS\Contracts\Action;

class ElaborateVideo extends Action
{
    protected $canFail = true;

    public function run($descriptor)
    {
        \Log::info('Elaborate video action called');

        dispatch(new ConvertVideo($descriptor));
        
        return $descriptor;
    }
}
