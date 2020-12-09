<?php

namespace KBox\View\Components;

use Illuminate\Http\Request;
use Illuminate\View\Component;
use KBox\Sorter;

class Sort extends Component
{
    public $isSearch = false;
    
    public $sorter;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(?Sorter $sorter = null)
    {
        $this->sorter = $sorter;
        $this->isSearch = app()->make(Request::class)->hasSearch();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.sort');
    }
}
