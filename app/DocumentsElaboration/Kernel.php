<?php

namespace KlinkDMS\DocumentsElaboration;

use KlinkDMS\DocumentDescriptor;
use Illuminate\Pipeline\Pipeline;

class Kernel
{

    /**
     * The application's global document elaboration stack.
     *
     * @var array
     */
    protected $actions = [
        \KlinkDMS\DocumentsElaboration\Actions\AddToSearch::class,
        \KlinkDMS\DocumentsElaboration\Actions\GenerateThumbnail::class,
    ];

    /**
     *
     * @param \KlinkDMS\DocumentDescriptor $documentDescriptor
     * @return \KlinkDMS\DocumentDescriptor
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
