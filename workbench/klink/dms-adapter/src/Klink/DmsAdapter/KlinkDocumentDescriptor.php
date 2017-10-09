<?php

namespace Klink\DmsAdapter;

use KlinkDMS\DocumentDescriptor;
use KSearchClient\Model\Data\Data;
use KSearchClient\Model\Data\Author;
use KSearchClient\Model\Data\Uploader;
use KSearchClient\Model\Data\Copyright;
use KSearchClient\Model\Data\Properties;
use Klink\DmsAdapter\KlinkVisibilityType;
use KSearchClient\Model\Data\CopyrightOwner;
use KSearchClient\Model\Data\CopyrightUsage;

/**
 * The mapping between DocumentDescriptor and K-Search Data model
 */
final class KlinkDocumentDescriptor
{
	
	/**
	 * visibility
	 * @var KlinkVisibilityType
	 */
	private $visibility = KlinkVisibilityType::KLINK_PRIVATE;
	
	private $descriptor = null;
	
	private $collections = [];

	/**
	 * The parameter-less constructor is used for deserialization 
	 * purposes only and might be deprecated in future versions
	 * @internal
	 */
	function __construct(DocumentDescriptor $descriptor = null, $visibility = KlinkVisibilityType::KLINK_PRIVATE){
		$this->descriptor = $descriptor;
		$this->visibility = $visibility;
	}


	public function getKlinkId() {
        return $this->descriptor->uuid;
	}
	public function uuid() {
        return $this->descriptor->uuid;
	}


	/**
	 * getVisibility
	 * @return KlinkVisibilityType
	 */
	public function getVisibility() {
		return $this->visibility;
	}
	
	public function visibility() {
		return $this->visibility;
	}


	public function hash()
	{
		return $this->descriptor->hash;
	}
	
	public function setHash($hash)
	{
		$this->descriptor->hash = $hash;
		return $this;
	}

	public function collections()
	{
		return $this->collections;
	}
	
	public function setCollections($collections)
	{
		$this->collections = $collections;
		return $this;
	}


	public function toData()
	{
		$data = new Data();
        $data->hash = $this->descriptor->hash;
        $data->type = $this->descriptor->mime_type === 'video/mp4' ? 'video' : 'document';
        $data->url = $this->descriptor->document_uri;
        $data->uuid = $this->descriptor->uuid;

		$authors = empty($this->descriptor->authors) ? [$this->descriptor->user_owner] : explode(',', $this->descriptor->authors);

		// Author is a required field, so if no authors are inserted by humans I will add the owner as an author. 
        $data->author = array_filter(array_map(function($author_string){
			$splitted = explode('<', $author_string);
			$author = new Author();
			$author->name = trim($splitted[0]);
			$author->email = rtrim(trim($splitted[1]), '>');
			return $author;
		}, $authors));

		$uploader = new Uploader();
		// TODO: this is only a default value for initial usage, this must be changed to reflect the uploader that should be shown
        $uploader->name = $this->descriptor->user_owner;
        $uploader->url = url('/');

        $data->uploader = $uploader;

		// considering that copyright options are not configurable, 
		// the data is marked with full copyright and the owner is 
		// specified by the URL of the K-Box instance
		$data->copyright = new Copyright();
        $data->copyright->owner = new CopyrightOwner();
        $data->copyright->owner->contact = url('/');
        $data->copyright->usage = new CopyrightUsage();
        $data->copyright->usage->short = 'C';
        $data->copyright->usage->name = 'All right reserved';
        $data->copyright->usage->reference = '';

        $data->properties = new Properties();
        $data->properties->title = $this->descriptor->title;
        $data->properties->filename = $this->descriptor->isMine() ? $this->descriptor->file->name : $this->descriptor->title;
		$data->properties->mime_type = $this->descriptor->mime_type;
		// TODO: setting language to en if not specified. This is due from a change in the K-Search API that do not extract language automatically
		$data->properties->language = !is_null($this->descriptor->language) && $this->descriptor->language !== 'unknown' ? $this->descriptor->language : 'en';
		$data->properties->collection = $this->collections;
		
		$created_at = $this->descriptor->created_at ? $this->descriptor->created_at : \Carbon\Carbon::now();
		$updated_at = $this->descriptor->updated_at ? $this->descriptor->updated_at : \Carbon\Carbon::now();

        $data->properties->created_at = new \DateTime($created_at->format('Y-m-d H:i:s.u'), $created_at->getTimezone());
        $data->properties->updated_at = new \DateTime($updated_at->format('Y-m-d H:i:s.u'), $updated_at->getTimezone());
        $data->properties->size = $this->descriptor->isMine() ? $this->descriptor->file->size : 0;
        $data->properties->abstract = $this->descriptor->abstract;
        $data->properties->thumbnail = $this->descriptor->thumbnail_uri;

        return $data;
	}



	/**
	 * Build an instance of KlinkDocumentDescriptor
	 * 
	 */
	public static function make(DocumentDescriptor $descriptor, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
	{
        if(empty($visibility)){
			$visibility = KlinkVisibilityType::KLINK_PRIVATE;
		}

        $instance = new self($descriptor, $visibility);
        return $instance;
    }


}
