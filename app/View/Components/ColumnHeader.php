<?php

namespace KBox\View\Components;

use Illuminate\Http\Request;
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
    public function __construct(Request $request, $key = null, $sort = null)
    {
        $this->key = $key;
        $this->sort = $sort;
        $this->isSearch = $request->hasSearch();
    }

    public function isSortable()
    {
        return ! is_null($this->key ?? null)
        && ! is_null($this->sort ?? null)
        && $this->sort->isSortable($this->key);
    }

    public function isSortEnabled()
    {
        return $this->isSortable() &&
            (! $this->isSearch && $this->sort->isEnabled($this->key) || $this->isSearch && $this->sort->isEnabledWithSearch($this->key));
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
