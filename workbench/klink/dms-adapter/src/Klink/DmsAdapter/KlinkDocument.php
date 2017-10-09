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
	 * @param string|stream $data The document data as string, stream (please don't close it until we have done) or the absolute file path of the document content
	 */
	public function __construct(KlinkDocumentDescriptor $descriptor, $data = ''){

		$this->descriptor = $descriptor;

		$this->documentData = $data;
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
	 * If you created the KlinkDocument passing a stream please use getDocumentStream() or getDocumentBase64Stream(). This method will use more RAM to do the same operation.
	 *
	 * @return string the base64 encoded content of the document, if a file was passed as content the encoded content of the file is returned
	 */
	public function getDocumentData() {

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
    
    /**
     * Get the content of the document, encoded as base64, as a stream
     *
     * This method returns a stream, so be sure to close it when you are done.
     *
     * PLEASE BE AWARE THAT THIS METHOD ALWAYS RETURNS A NEW STREAM WHEN INVOKED
     *
     * @return resource the document content as a raw stream
     * @throws UnexpectedValueException if the internal document data was a stream and has been closed
     */
    public function getDocumentBase64Stream()
    {
        if (!is_string($this->documentData) && !is_resource($this->documentData)) {
            throw new \UnexpectedValueException('The original document should be a string or a resource');
        }

        if (is_resource($this->documentData)) {
            $resourceType = get_resource_type($this->documentData);

            if ('stream' === $resourceType) {
                rewind($this->documentData);

                $fp = tmpfile();
                stream_filter_append($fp, 'convert.base64-encode', STREAM_FILTER_WRITE);
                stream_copy_to_stream($this->documentData, $fp);
                rewind($fp);

                return $fp;
            }

            // If the resource is not a stream, something is wrong here.
            throw new \UnexpectedValueException('The original document resource is not a stream');
        }

        if($this->isFile()){

			$fp = tmpfile();
			
			$src = fopen($this->documentData, 'r');

			stream_filter_append($fp, 'convert.base64-encode', STREAM_FILTER_WRITE);
			stream_copy_to_stream($src, $fp);
			rewind($fp);

			fclose($src);

			return $fp;
        }

		$fp = tmpfile();

		stream_filter_append($fp, 'convert.base64-encode', STREAM_FILTER_WRITE);
		fwrite($fp, $this->documentData);
		rewind($fp);

        return $fp;
    }
}
