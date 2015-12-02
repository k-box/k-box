<?php namespace Klink\DmsDocuments;

use Illuminate\Support\Collection;
use KlinkDMS\Pagination\LengthAwarePaginator as Paginator;

/**
 * What's in the users trash, but a little bit structured.
 */
class TrashContentResponse {
	
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
	 * @return Collection
	 */
	public function all(){
		return $this->documents->merge($this->collections);
	}
	
	/**
	 * @return Collection
	 */
	public function documents(){
		return $this->documents;
	}
	
	/**
	 * @return Collection
	 */
	public function collections(){
		return $this->collections;
	}
	
	/**
	 * @return LengthAwarePaginator|null
	 */
	public function paginator(){
		return $this->paginator;
	}
	

}