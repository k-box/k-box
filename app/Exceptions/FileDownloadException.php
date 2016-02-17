<?php namespace KlinkDMS\Exceptions;

use Exception;
use KlinkDMS\File;


/**
 * Raised when a file cannot be downloaded. 
 */
final class FileDownloadException extends Exception
{
    protected $ref_file = null;
	
    function __construct($message, File $file = null) {
		parent::__construct( $message, 1024 );
        
        $this->ref_file = $file;
	}
    
    
    public function file(){
        return $this->ref_file;
    }
	
}