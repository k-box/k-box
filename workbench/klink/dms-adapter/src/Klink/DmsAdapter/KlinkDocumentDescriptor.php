<?php

namespace Klink\DmsAdapter;

use KBox\DocumentDescriptor;
use KBox\Option;
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
use KSearchClient\Model\Data\Properties\Streaming as VideoStreaming;

/**
 * The mapping between DocumentDescriptor and K-Search Data model
 */
final class KlinkDocumentDescriptor
{
	const DATA_TYPE_DOCUMENT = Data::DATA_TYPE_DOCUMENT;
	const DATA_TYPE_VIDEO = Data::DATA_TYPE_VIDEO;
	const AUTHORS_REGEXP = '/(.*)\s(<?\S*@\S*>?)/m'; // format: name followed by a space and then the email address (optionally within angle brackets)
	
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

		$file_properties = $this->descriptor->isMine() ? $this->descriptor->file->properties : collect();
		$data_type = $this->descriptor->mime_type === 'video/mp4' ? self::DATA_TYPE_VIDEO : self::DATA_TYPE_DOCUMENT;
		$data = new Data();
        $data->hash = $this->descriptor->hash;
        $data->type = $data_type;
        $data->url = $this->buildDownloadUrl();
		$data->uuid = $this->descriptor->uuid;
		$data->geo_location = Geometries::boundingBoxFromGeoserver($file_properties->get('boundings.geoserver', null)) ?? $file_properties->get('boundings.geojson', null); // add location information if expressed in the file properties
		
		$authors = array_from($this->descriptor->authors ?? []);
		
		$processed_authors = array_filter(array_map(function($author_string){
			preg_match_all(self::AUTHORS_REGEXP, $author_string, $matches, PREG_SET_ORDER, 0);
			
			if(count($matches) !== 1){
				return false;
			}
			$author = new Author();
			$author->name = trim($matches[0][1]);
			$author->email = ltrim(rtrim($matches[0][2], '>'), '<');
			return $author;
		}, $authors));

        $data->authors = $processed_authors;
		
		$uploader = new Uploader();

		// The uploader must be anonymized, therefore if set we use 
		// the SHA-1 of the identifier and the name combined
		$uploader->name = $this->descriptor->owner ? sha1($this->descriptor->owner->getKey()) : null;
		$uploader->url = url('/');
		
        $data->uploader = $uploader;

		$data->copyright = new Copyright();
		$data->copyright->owner = new CopyrightOwner();

		$copyright_owner = $this->descriptor->copyright_owner;

		$owner_name = $copyright_owner->get('name', '');
		
        $data->copyright->owner->name = empty(trim($owner_name)) ? '-' : $owner_name;
        $data->copyright->owner->website = $copyright_owner->get('website', '');
        $data->copyright->owner->email = $copyright_owner->get('email', '');
        $data->copyright->owner->address = $copyright_owner->get('address', '');
		
		$usage_license = $this->descriptor->copyright_usage ?? Option::copyright_default_license();
		
		$data->copyright->usage = new CopyrightUsage();
        $data->copyright->usage->short = $usage_license->id;
        $data->copyright->usage->name = $usage_license->name;
        $data->copyright->usage->reference = $usage_license->license;
		
        $data->properties = new Properties();
        $data->properties->title = $this->descriptor->title;
        $data->properties->filename = $this->descriptor->isMine() ? $this->descriptor->file->name : $this->descriptor->title;
		$data->properties->mime_type = $this->removeCharsetFromMimeType($this->descriptor->mime_type);
		$data->properties->language = !is_null($this->descriptor->language) && $this->descriptor->language !== 'unknown' ? $this->descriptor->language : '__';
			// using __ to define that no language is specified for that document
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

			// if public is requested and has a streaming URL
			
			if($this->visibility === KlinkVisibilityType::KLINK_PUBLIC && ($this->descriptor->hasPendingPublications() || $this->descriptor->isPublished()) ){
				
				$publication = $this->descriptor->publication();

				if($publication && $publication->streaming_url){

					$streaming_properties = tap(new VideoStreaming(), function($stream) use ($publication){
						$stream->type = 'dash';
						$stream->url = $publication->streaming_url;
					});
					
					$video_properties->streaming = [$streaming_properties];
				}

			}
			
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
	
	private function removeCharsetFromMimeType($mime_type)
	{
		$charset_pos = strpos($mime_type, ';');
		$mime = $charset_pos !== false ? trim(substr($mime_type, 0, $charset_pos)) : $mime_type;
		return $mime;
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
