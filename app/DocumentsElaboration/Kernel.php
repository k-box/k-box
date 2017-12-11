<?php

namespace KBox\DocumentsElaboration;

use KBox\DocumentDescriptor;
use Illuminate\Pipeline\Pipeline;

class Kernel
{

    /**
     * The application's global document elaboration stack.
     *
     * @var array
     */
    protected $actions = [
        \KBox\DocumentsElaboration\Actions\ExtractFileProperties::class,
        \KBox\DocumentsElaboration\Actions\GuessLanguage::class,
        \KBox\DocumentsElaboration\Actions\AddToSearch::class,
        \KBox\DocumentsElaboration\Actions\GenerateThumbnail::class,
        \KBox\DocumentsElaboration\Actions\ElaborateVideo::class,
    ];

    /**
     *
     * @param \KBox\DocumentDescriptor $documentDescriptor
     * @return \KBox\DocumentDescriptor
     */
    public function handle($documentDescriptor)
    {
        return app(Pipeline::class)
            ->send($documentDescriptor)
            ->through($this->actions)
            ->then(function ($descriptor) {
                if ($descriptor->status !== DocumentDescriptor::STATUS_ERROR) {
                    $descriptor->status = DocumentDescriptor::STATUS_COMPLETED;
    
                    $descriptor->save();
                }

                return $descriptor->fresh();
            });
    }
}
