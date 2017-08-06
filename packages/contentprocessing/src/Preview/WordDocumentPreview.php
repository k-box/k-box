<?php

namespace Content\Preview;

use Content\Contracts\Preview as PreviewContract;
use PhpOffice\PhpWord\IOFactory;
use Content\FileProperties;

/**
 * Word processing document preview
 */
class WordDocumentPreview implements PreviewContract
{
    private $path = null;

    /**
     * The HTML writer instance
     */
    private $writer = null;
    
    /**
     * The Document instance
     */
    private $document = null;
    
    public function __construct()
    {
    }

    public function load($path)
    {
        $this->path = $path;

        $this->document = IOFactory::load($this->path);

        $this->writer = IOFactory::createWriter($this->document, 'HTML');

        return $this;
    }

    public function css()
    {
        return null;
    }

    public function html()
    {
        $content = $this->writer->getWriterPart('Body')->write();
        
        $content = str_replace('<body>', '', $content);
        $content = str_replace('</body>', '', $content);

        return sprintf('<div class="preview__render preview__render--document">%1$s</div>',
                $content);
    }

    public function properties()
    {
        $properties = $this->document->getDocInfo();

        $prop = new FileProperties();
        $prop->setTitle(e($properties->getTitle()))
             ->setCreator(e($properties->getCreator()))
             ->setDescription(e($properties->getDescription()))
             ->setSubject(e($properties->getSubject()))
             ->setCreatedAt($properties->getCreated())
             ->setModifiedAt($properties->getModified())
             ->setLastModifiedBy(e($properties->getLastModifiedBy()))
             ->setKeywords(e($properties->getKeywords()))
             ->setCategory(e($properties->getCategory()))
             ->setCompany(e($properties->getCompany()));
        
        return $prop;
    }

    public function supportedMimeTypes()
    {
        return [];
    }
}
