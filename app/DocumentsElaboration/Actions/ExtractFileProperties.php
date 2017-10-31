<?php

namespace KlinkDMS\DocumentsElaboration\Actions;

use KlinkDMS\Contracts\Action;
use OneOffTech\VideoProcessing\VideoProcessorFactory;

class ExtractFileProperties extends Action
{
    protected $canFail = true;

    public function run($descriptor)
    {
        $file = $descriptor->file;

        \Log::info("Extracting file properties action triggered: {$descriptor->id}, file: $file->id");
        
        if ($file->isVideo()) {

            // todo: isolate this in a specific service, so can be used for multiple file types

            $videoProcessor = app()->make(VideoProcessorFactory::class)->make();
            
            if ($videoProcessor->isInstalled()) {
                $out = $videoProcessor->details($file->absolute_path);

                \Log::info('video details', ['out' => $out]);
                $file->properties = json_decode($out);
            }
        }

        $file->save();
        
        return $descriptor;
    }
}
