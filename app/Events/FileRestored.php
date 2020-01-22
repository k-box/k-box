<?php

namespace KBox\Events;

use KBox\File;

/**
 * File restored event
 *
 * It is fired when a file is restored from trash.
 */
class FileRestored extends CausedEvent
{

    /**
     * The file that has been restored
     * @var KBox\File
     */
    public $file;

    /**
     * Create a new event instance.
     *
     * @param KBox\File The file that has been restored
     * @return void
     */
    public function __construct(File $file)
    {
        parent::__construct();

        $this->file = $file;
    }
}
