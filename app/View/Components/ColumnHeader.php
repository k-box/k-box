<?php

namespace KBox\View\Components;

use Illuminate\View\Component;

class ColumnHeader extends Component
{
    public $key;
    
    public $sort;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($key = null, $sort = null)
    {
        $this->key = $key;
        $this->sort = $sort;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.column-header');
    }
}
