<?php

namespace KBox\Exceptions;

use Exception;
use KBox\DocumentDescriptor;
use KBox\File;
use KBox\User;

/**
* States that a file already exists in the system
*/
class FileAlreadyExistsException extends Exception
{
    
    /**
     * @var DocumentDescriptor
     */
    private $existing_descriptor = null;

    /**
     * @var File
     */
    private $file_revision = null;

    /**
     * Creates a new File already Exists exception instance.
     *
     * @param string $upload_name the name of the file you are trying to upload in the system
     * @param DocumentDescriptor $descr the document descriptor of the existing file
     * @param File $file (optional) the exact {@see File} revision of the document descriptor
     * @return FileAlreadyExistsException
     */
    public function __construct($upload_name, DocumentDescriptor $descr = null, File $file = null)
    {
        parent::__construct(trans('errors.filealreadyexists.generic', [
            'name' => e($upload_name),
            'title' => is_null($descr) && ! is_null($file) ? e($file->name) : (is_null($descr) && is_null($file) ? e($upload_name) : e($descr->title))
        ]), 409);
        
        $this->existing_descriptor = $descr;
        
        if (! is_null($file)) {
            $this->file_revision = $file;
        }
    }
    
    /**
     * Set the DocumentDescriptor of the same copy of the file that is already available in the system
     */
    public function setDescriptor(DocumentDescriptor $descr)
    {
        $this->existing_descriptor = $descr;
        return $this;
    }
    
    /**
     * The DocumentDescriptor of the document already existing
     *
     * @return DocumentDescriptor
     */
    public function getDescriptor()
    {
        return $this->existing_descriptor;
    }

    /**
     * Set the File revision, used in case the already existing file
     * is an old revision of a document
     */
    public function setFileVersion(File $file)
    {
        $this->file_revision = $file;
        return $this;
    }
    
    /**
     * The File revision, used in case the already existing file
     * is an old revision of a document
     *
     * @return File
     */
    public function getFileVersion()
    {
        return $this->file_revision;
    }

    /**
     * Construct the proper message to show to the user based on the
     * DocumentDescriptor attached to the issue and the User specified
     * as parameter
     *
     * @return string the issue message already localized
     */
    public function render(User $user)
    {
        if (is_null($this->existing_descriptor)) {
            return $this->getMessage();
        }

        if ($this->existing_descriptor->owner_id === $user->id) {
            $collection = $this->existing_descriptor->groups->first();

            if (! is_null($collection)) {
                return trans('errors.filealreadyexists.incollection_by_you', [
                    'title' => e($this->existing_descriptor->title),
                    'collection' => e($collection->name),
                    'collection_link' => route('documents.groups.show', [ 'id' => $collection->id, 'highlight' => $this->existing_descriptor->id])
                ]);
            }

            if (! is_null($this->file_revision)) {
                return trans('errors.filealreadyexists.revision_of_your_document', [
                    'title' => e($this->existing_descriptor->title)
                ]);
            }

            return trans('errors.filealreadyexists.by_you', [
                'title' => e($this->existing_descriptor->title)
            ]);
        } elseif (! is_null($this->existing_descriptor->owner_id)) {
            $collection = $this->existing_descriptor->groups()->public()->first();
            $owner = $this->existing_descriptor->owner;

            if (! is_null($collection)) {
                return trans('errors.filealreadyexists.incollection', [
                    'title' => e($this->existing_descriptor->title),
                    'collection' => e($collection->name),
                    'collection_link' => route('documents.groups.show', [ 'id' => $collection->id, 'highlight' => $this->existing_descriptor->id])
                ]);
            }

            if (! is_null($this->file_revision)) {
                return trans('errors.filealreadyexists.revision_of_document', [
                    'title' => e($this->existing_descriptor->title),
                    'user' => e($owner->name),
                    'email' => e($owner->email)
                ]);
            }

            return trans('errors.filealreadyexists.by_user', [
                'user' => e($owner->name),
                'email' => e($owner->email)
            ]);
        }
        
        if ($this->existing_descriptor->isPublished()) {
            return trans('errors.filealreadyexists.in_the_network', [
                'network' => e(network_name()),
                'title' => e($this->existing_descriptor->title),
                'institution' => config('dms.institutionID')
            ]);
        }
    }
}
