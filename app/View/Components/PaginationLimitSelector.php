<?php

namespace KBox\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class PaginationLimitSelector  extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $routeName;
    
    public $routeParamId;

    public $search_replica_parameters;

    public $pageParams;
    
    public $optionItemsPerPage;

    public function __construct($pageParams = [])
    {
        $this->routeName = $this->getRouteName();

        $this->optionItemsPerPage = auth()->user()->optionItemsPerPage();
        
        $this->pageParams =array_merge(

            $pageParams,
            [
                'group' => $this->getParamId(),
                'search_replica_parameters' => request()->only('s')
            ]

        );
    }

    private function getRouteName(){

        return  Route::current()->getName();
        
    }

    private function getParamId(){

        if(! Route::current()->parameter('group')) {
            return null;
        } 

        return  Route::current()->parameter('group');

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
