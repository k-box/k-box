<?php

namespace KBox\Documents\Preview;

use KBox\Documents\Contracts\Preview as PreviewContract;
use KBox\Documents\Presentation\SlideRenderer;
use KBox\Documents\Presentation\Reader\PowerPoint2007;
use KBox\Documents\Presentation\PresentationProperties;

use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\PhpPresentation;

/**
 *
 */
class PresentationPreview implements PreviewContract
{
    private $path = null;

    // /**
    //  * The HTML writer instance
    //  */
    // private $writer = null;
    
    /**
     * The Presentation instance
     * @var PhpPresentation
     */
    private $presentation = null;
    
    public function __construct()
    {
    }

    public function load($path)
    {
        $this->path = $path;
        // $pptReader = IOFactory::createReader('PowerPoint2007');
        $pptReader = new PowerPoint2007();
        $this->presentation = $pptReader->load($this->path);

        return $this;
    }

    public function css()
    {
        return null;
    }

    public function html()
    {

        // Fetch slides
        $slides = $this->presentation->getAllSlides();

        $layout_class = (empty($this->presentation->getLayout()->getDocumentLayout()) ?
                    'presentation--custom' :
                    $this->toClassName($this->presentation->getLayout()->getDocumentLayout()));

        // Construct HTML
        $html = '<section id="slides" class="presentation '.$layout_class.'">';

        $totalSlides = count($slides);

        if ($totalSlides > 0) {
            $slideIndex = 1;

            $slide = null;

            for ($i=0; $i < $totalSlides; $i++) {
                $slide = $slides[$i];
                $html .= (new SlideRenderer($slide, $slideIndex++))->render();
            }
        }

        $html .= '</section>'.PHP_EOL;

        return $html;
    }

    /**
     * The hierarchy of the presentation
     */
    public function getNavigation()
    {
        $this->load();

        // Fetch slides
        $slides = $this->presentation->getAllSlides();

        // Construct HTML
        $html = '';

        if (count($slides) > 0) {
            // Loop all sheets

            $html .= '<ul class="navigation">'.PHP_EOL;
            $slideIndex = 0;
            foreach ($slides as $slide) {
                $html .= '  <li class="navigation__slide '.(! $slide->isVisible() ? 'navigation__slide-hidden':'').'"><a href="#slide'.$slideIndex.'">'.$slide->getName().' ('.$slideIndex.':'.$slide->getHashCode().')</a></li>'.PHP_EOL;
                ++$slideIndex;
            }

            $html .= '</ul>'.PHP_EOL;
        }

        return $html.'<hr/>';
    }

    /**
     *
     * @return PresentationProperties
     */
    public function properties()
    {
        $properties = $this->presentation->getDocumentProperties();

        $prop = new PresentationProperties();
        $prop->setTitle(e($properties->getTitle()))
             ->setCreator(e($properties->getCreator()))
             ->setDescription(e($properties->getDescription()))
             ->setSubject(e($properties->getSubject()))
             ->setCreatedAt($properties->getCreated())
             ->setModifiedAt($properties->getModified())
             ->setLastModifiedBy(e($properties->getLastModifiedBy()))
             ->setKeywords(e($properties->getKeywords()))
             ->setCategory(e($properties->getCategory()))
             ->setCompany(e($properties->getCompany()))
             ->setTotalSlides($this->presentation->getSlideCount())
             ->setLayout((empty($this->presentation->getLayout()->getDocumentLayout()) ? 'Custom' : $this->presentation->getLayout()->getDocumentLayout()))
             ->setHeight($this->presentation->getLayout()->getCY(DocumentLayout::UNIT_MILLIMETER))
             ->setWidth($this->presentation->getLayout()->getCX(DocumentLayout::UNIT_MILLIMETER));
        
        return $prop;
    }

    public function supportedMimeTypes()
    {
        return [];
    }

    protected function toClassName($string)
    {
        $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        return strtolower($slug);
    }
}
