<?php

namespace KBox\Documents;

use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

/**
 * What's in the users trash, but a little bit structured.
 */
class TrashContentResponse
{
    private $documents = null;
    private $collections = null;
    private $paginator = null;

    public function __construct($documents, $collections, $paginator)
    {
        $this->documents = $documents;
        $this->collections = $collections;
        $this->paginator = $paginator;
    }
    
    /**
     * Return both documents and collections
     *
     * @return Collection
     */
    public function all()
    {
        return $this->documents->merge($this->collections);
    }
    
    /**
     * Return all documents
     *
     * @return Collection
     */
    public function documents()
    {
        return $this->documents;
    }
    
    /**
     * Return all collections
     *
     * @return Collection
     */
    public function collections()
    {
        return $this->collections;
    }
    
    /**
     * @return LengthAwarePaginator|null
     */
    public function paginator()
    {
        return $this->paginator;
    }
}
