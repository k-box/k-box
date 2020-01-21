<?php

namespace KBox\Events;

use KBox\DocumentDescriptor;
use KBox\File;

class DocumentVersionUploaded extends CausedEvent
{
    
    /**
     * @var \KBox\File
     */
    public $file;
    
    /**
     * @var \KBox\DocumentDescriptor
     */
    public $descriptor;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DocumentDescriptor $descriptor, File $file)
    {
        parent::__construct();

        $this->descriptor = $descriptor;
        
        $this->file = $file;
    }
}
