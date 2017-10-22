<?php

namespace Klink\DmsAdapter;

/**
 * Describe a K-Link Document. 
 * This class is used for send documents to the K-Link Core for indexing purposes
 */
class KlinkDocument {

	/**
	 * descriptor
	 * @var KlinkDocumentDescriptor
	 */

	protected $descriptor;

	/**
	 * content, stream or file path
	 * @var mixed
	 */

	protected $documentData;

	/**
	 * Create an instance of a KlinkDocument
	 *
	 * @param KlinkDocumentDescriptor $descriptor The descriptor of the document
	 * @param string|stream $alternateData The alternate content for the document as string or stream (please don't close it until we have done). It will be used in case the file type is not supported
	 */
	public function __construct(KlinkDocumentDescriptor $descriptor, $alternateData = ''){

		$this->descriptor = $descriptor;

		$this->documentData = $alternateData;
	}

	/**
	 * Returns the KlinkDocument descriptor for this document
	 *
	 * @return KlinkDocumentDescriptor
	 */
	public function getDescriptor() {

		return $this->descriptor;
	}

	/**
	 * Tells if the document data, hold by this instance, is a file on disk
	 *
	 * @return boolean true if the data hold is a file, false otherwise
	 */
	function isFile(){
		
		if(empty($this->documentData)){
			return false;
		}

		if(is_object($this->documentData)){
			return false;
		}
		
		if(is_resource($this->documentData) && @get_resource_type($this->documentData) === 'stream'){
			return false;
		}
		
		return @is_file($this->documentData);
	}
	
	/**
	 * Return the internal representation of the document data, as passed in the constructor
	 * @return mixed the document data
	 */
	public function getOriginalDocumentData(){
		return $this->documentData;
	}

	/**
	 * Get the full document content as a base64 string
	 *
	 * If you created the KlinkDocument passing a stream please use getDocumentStream().
	 *
	 * @return string|null the plain content of the document, if the descriptor type is not supported, null otherwise
	 */
	public function getDocumentData() {

		// if file is supported return null
		if($this->descriptor->file()->isIndexable()){
			return null;
		}

		if(is_resource($this->documentData) && @get_resource_type($this->documentData) === 'stream'){
			
			return stream_get_contents($this->documentData);
		}

		if($this->isFile()){

			return file_get_contents( $this->documentData );

		}

		return $this->documentData;
	}
    
    /**
	 * Get the content of the document as a stream
	 *
	 * This method returns a stream, so be sure to close it when you are done.
	 *
	 * please be aware that this method returns a new stream unless the KlinkDocument was created using a stream. In this last case the original stream is returned
	 *
	 * @return stream the document content as a raw readonly stream
	 * @throws UnexpectedValueException if the internal document data was already a stream and has been closed
	 */
	public function getDocumentStream() {
		
		if( is_resource($this->documentData) && @get_resource_type($this->documentData) === 'stream' ){
			rewind($this->documentData);
			return $this->documentData;
		}
		else if( @get_resource_type($this->documentData) === 'Unknown' ){
			throw new \UnexpectedValueException('The original document stream is closed');
		}
		
		if($this->isFile()){
			return fopen($this->documentData, 'r');
		}
		
		return fopen('data://text/plain,' . $this->documentData, 'r');
		
	}
    
}
