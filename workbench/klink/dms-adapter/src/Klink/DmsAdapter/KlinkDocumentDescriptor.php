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
use KSearchClient\Model\Data\Properties\Video as VideoProperties;
use KSearchClient\Model\Data\Properties\Audio as AudioProperties;
use KSearchClient\Model\Data\Properties\Source as VideoSource;

/**
 * The mapping between DocumentDescriptor and K-Search Data model
 */
final class KlinkDocumentDescriptor
{
	const DATA_TYPE_DOCUMENT = Data::DATA_TYPE_DOCUMENT;
	const DATA_TYPE_VIDEO = Data::DATA_TYPE_VIDEO;
	
	/**
	 * visibility
	 * @var KlinkVisibilityType
	 */
	private $visibility = KlinkVisibilityType::KLINK_PRIVATE;
	
	private $descriptor = null;
	
	private $collections = [];
	
	private $projects = [];

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
	
	public function mime_type() {
        return $this->descriptor->mime_type;
	}

	public function file() {
        return $this->descriptor->file;
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

	public function projects()
	{
		return $this->projects;
	}
	
	public function setProjects($projects)
	{
		$this->projects = $projects;
		return $this;
	}

	private function buildDownloadUrl()
	{
		if($this->visibility === KlinkVisibilityType::KLINK_PUBLIC){
			return $this->descriptor->document_uri;
		}

		if(config('app.url') !== config('app.internal_url')){
			return rtrim(config('app.internal_url'), '/') . "/files/{$this->descriptor->file->uuid}?t={$this->descriptor->file->generateDownloadToken()}";
		}

		return url("/files/{$this->descriptor->file->uuid}?t={$this->descriptor->file->generateDownloadToken()}", [], false); 
	}

	public function toData()
	{
		// grab the information of the data type to be used to express properties on the search

		$file_properties = $this->descriptor->isMine() && $this->descriptor->file->properties ? (collect(array_dot($this->descriptor->file->properties)) ?? collect()) : collect();
		$data_type = $this->descriptor->mime_type === 'video/mp4' ? self::DATA_TYPE_VIDEO : self::DATA_TYPE_DOCUMENT;
		$data = new Data();
        $data->hash = $this->descriptor->hash;
        $data->type = $data_type;
        $data->url = $this->buildDownloadUrl();
        $data->uuid = $this->descriptor->uuid;
		
		$user_owner = $this->descriptor->user_owner;
		$authors = empty($this->descriptor->authors) ? [$this->descriptor->user_owner] : explode(',', $this->descriptor->authors);
		
		$processed_authors = array_filter(array_map(function($author_string){
			$author_string = str_replace('&lt;', '<', str_replace('&gt;', '>',$author_string ));
			$splitted = explode('<', $author_string);

			if(count($splitted) !== 2){
				return false;
			}
			$author = new Author();
			$author->name = trim($splitted[0]);
			$author->email = rtrim(trim($splitted[1]), '>');
			return $author;
		}, $authors));

		if(empty($processed_authors)){
			$processed_authors[] = tap(new Author, function($a) use($user_owner){
				$splitted = explode('<', $user_owner);
				$a->name = trim($splitted[0]);
				$a->email = rtrim(trim($splitted[1]), '>');
			});
		}

		// Author is a required field, so if no authors are inserted by humans I will add the owner as an author. 
        $data->authors = $processed_authors;
		
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
		$data->properties->collections = $this->collections;
		$data->properties->tags = $this->projects;
		
		$created_at = $this->descriptor->created_at ? $this->descriptor->created_at : \Carbon\Carbon::now();
		$updated_at = $this->descriptor->updated_at ? $this->descriptor->updated_at : \Carbon\Carbon::now();

        $data->properties->created_at = new \DateTime($created_at->format('Y-m-d H:i:s.u'), $created_at->getTimezone());
        $data->properties->updated_at = new \DateTime($updated_at->format('Y-m-d H:i:s.u'), $updated_at->getTimezone());
        $data->properties->size = $this->descriptor->isMine() ? $this->descriptor->file->size : 0;
        $data->properties->abstract = $this->descriptor->abstract;
		$data->properties->thumbnail = $this->descriptor->thumbnail_uri;
		
		if($data_type === self::DATA_TYPE_VIDEO){
			
			$video_properties = new VideoProperties();
			
			$video_properties->duration = $file_properties->get('duration', 'unknown');
			
			$video_properties->source = tap(new VideoSource(), function($source) use($file_properties){

				$format = array_filter([$file_properties->get('format_long', ''), $file_properties->get('video.codec', '')]);

				$source->format = implode(', ', $format);
				$source->resolution = $file_properties->get('video.resolution', 'unknown'); 
				$source->bitrate = $file_properties->get('bitrate', 'unknown'); 
			});
			
			$data->properties->video = $video_properties;
			
			if($file_properties->has('audio.codec') && $file_properties->has('audio.sample_rate')){

				$audio_properties = tap(new AudioProperties(), function($audio) use($file_properties){
					$audio->bitrate = $file_properties->get('audio.sample_rate', 'unknown');
					$audio->format = $file_properties->get('audio.codec', 'unknown');
				});

				$data->properties->audio = [$audio_properties];

			}
		}

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
