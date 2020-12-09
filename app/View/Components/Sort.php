<?php

namespace KBox\View\Components;

use Illuminate\Http\Request;
use Illuminate\View\Component;
use KBox\Sorter;

class Sort extends Component
{
    public $isSearch = false;
    
    public $sorter;

    public $sortables;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Request $request, ?Sorter $sorter = null)
    {
        $this->sorter = $sorter;
        $this->isSearch = $request->hasSearch();
        $this->sortables = $this->processSortables($sorter->sortables);
    }

    /**
     * Transform the sortables in an associative array that
     * represent the key and the status of enabled/disabled
     */
    private function processSortables($sortables)
    {
        return collect($sortables)->mapWithKeys(function ($opts, $key) {
            return [$key => $this->isSearch ? ! empty($opts[2] ?? null) : ! empty($opts[0] ?? false)];
        })->toArray();
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

    public function isCurrent($key, $order = null)
    {
        $current = $this->sorter->current($key);

        if (is_null($order)) {
            return $current;
        }

        return $current && $this->sorter->direction === $order;
    }
}
