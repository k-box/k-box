<?php

namespace KBox\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;

class SearchResultsPaginator extends LengthAwarePaginator
{
    protected $terms;

    protected $filters;
    
    protected $facets;

    /**
     * Create a new paginator instance.
     *
     * @param  mixed  $items
     * @param  int  $total
     * @param  int  $perPage
     * @param  int|null  $currentPage
     * @param  array  $options (path, query, fragment, pageName)
     * @return void
     */
    public function __construct($terms, $items, $filters, $facets, $total, $perPage, $currentPage = null, array $options = [])
    {
        parent::__construct($items, $total, $perPage, $currentPage, $options);
        
        $this->terms = $terms;
        $this->filters = $filters;
        $this->facets = $facets;
    }
    
    public function terms()
    {
        return $this->terms;
    }
    
    public function filters()
    {
        return $this->filters;
    }
    
    public function facets()
    {
        return $this->facets;
    }
    
    /**
     * Map the current search results item to other instances as defined by the callback content.
     *
     * @return SearchResultsPaginator the paginator with the items mapped from the previous instance to the one defined by the callback.
     */
    public function map(callable $callback)
    {
        $mapped = $this->getCollection()->map($callback);
        
        $this->items = $mapped;
        
        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'total'         => $this->total(),
            'per_page'      => $this->perPage(),
            'current_page'  => $this->currentPage(),
            'last_page'     => $this->lastPage(),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
            'from'          => $this->firstItem(),
            'to'            => $this->lastItem(),
            'data'          => $this->items->toArray(),
            'terms'         => $this->terms,
            'filters'       => $this->filters,
            'facets'        => $this->facets
        ];
    }
}
