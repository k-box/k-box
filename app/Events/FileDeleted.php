<?php

namespace KBox\Events;

use KBox\File;

/**
 * File deleted event
 *
 * It is fired when a file is trashed or permanently deleted.
 */
class FileDeleted extends CausedEvent
{

    /**
     * The file that has been deleted
     * @var KBox\File
     */
    public $file;

    /**
     * If the file was permanently deleted
     * @var bool
     */
    public $forceDeleted = false;

    /**
     * Create a new event instance.
     *
     * @param KBox\File The file that has been deleted
     * @return void
     */
    public function __construct(File $file)
    {
        parent::__construct();

        $this->file = $file;
        $this->forceDeleted = $file->isForceDeleting();
    }
}
