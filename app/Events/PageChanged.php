<?php

namespace KBox\Events;

use KBox\Pages\PageModel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * A Pages\Page has been changed
 */
class PageChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $page;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PageModel $page)
    {
        $this->page = $page;
    }
}
