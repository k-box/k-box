<?php

namespace KBox\Events;

use KBox\File;

/**
 * File deleting event
 *
 * It is fired immediately before a file is trashed or permanently deleted.
 */
class FileDeleting extends CausedEvent
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
