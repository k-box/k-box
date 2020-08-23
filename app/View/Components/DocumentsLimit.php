<?php

namespace KBox\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class DocumentsLimit extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    
    public $routeName;
    
    public $routeParamId;

    public $range;

    public $search_replica_parameters;

    public function __construct($range = "", $search_replica_parameters = [])
    {
        $this->routeName = $this->getRouteName();
        $this->routeParamId = $this->getParamId();
        $this->range = $range;
        $this->search_replica_parameters = $search_replica_parameters;

    }

    private function getRouteName(){

        return  Route::current()->getName();
        
    }

    private function getParamId(){

        if(! Route::current()->parameter('group')) {
            return [];
        } 

        return  ['group' => Route::current()->parameter('group')];

    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {

        return view('components.documents-limit');
    }

}
