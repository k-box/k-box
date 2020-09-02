<?php

namespace KBox\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class PaginationLimitSelector extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $routeName;
    
    public $routeParamId;

    public $pageParams;
    
    public $optionItemsPerPage;

    public function __construct($pageParams = [])
    {
        $this->routeName = $this->getRouteName();

        $this->optionItemsPerPage = auth()->user()->optionItemsPerPage();
        
        $this->pageParams =array_merge(
            $pageParams,
            request()->only('s'),
            $this->getParamId()
        );
    }

    private function getRouteName()
    {
        return  Route::current()->getName();
    }

    private function getParamId()
    {
        if (! Route::current()->parameter('group')) {
            return [];
        }

        return  ['group'=>Route::current()->parameter('group')];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.pagination-limit-selector');
    }
}
