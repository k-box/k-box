<?php namespace KlinkDMS\Exceptions;


use Exception;


/**
* States that a file already exists in the system
*/
class FileAlreadyExistsException extends Exception
{
	
	private $existing_file = null;
    
    
    public function setExistingFile($file){
        $this->existing_file = $file;
        return $this;
    }
    
    public function getExistingFile(){
        return $this->existing_file;
    }
    
    
}