<?php

namespace KBox\Documents\Preview;

use KBox\Documents\Presentation\SlideRenderer;
use KBox\Documents\Presentation\Reader\PowerPoint2007;
use KBox\Documents\Presentation\PresentationProperties;

use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\PhpPresentation;
use KBox\File;
use Illuminate\Contracts\Support\Renderable;

/**
 *
 */
class PresentationPreview extends BasePreviewDriver implements Renderable
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

    protected function load($path)
    {
        $this->path = $path;
        // $pptReader = IOFactory::createReader('PowerPoint2007');
        $pptReader = new PowerPoint2007();
        $this->presentation = $pptReader->load($this->path);

        return $this;
    }

    public function __destruct()
    {
        if ($this->presentation) {
            $this->presentation = null;
        }
    }

    public function preview(File $file) : Renderable
    {
        $this->load($file->absolute_path);

        return $this;
    }

    public function render()
    {

        // Fetch slides
        $slides = $this->presentation->getAllSlides();

        $layout_class = (empty($this->presentation->getLayout()->getDocumentLayout()) ?
                    'presentation--custom' :
                    $this->toClassName($this->presentation->getLayout()->getDocumentLayout()));

        // Construct HTML
        $html = '<section id="slides" class="presentation relative max-w-6xl w-full '.$layout_class.'">';

        $totalSlides = count($slides);

        $coordinatesReferenceSystem = [
            0,
            0,
            $this->presentation->getLayout()->getCX(DocumentLayout::UNIT_PIXEL),
            $this->presentation->getLayout()->getCY(DocumentLayout::UNIT_PIXEL),
        ];

        if ($totalSlides > 0) {
            $slide = null;

            for ($i=0; $i < $totalSlides; $i++) {
                $slide = $slides[$i];
                $html .= (new SlideRenderer($slide, $i+1, $coordinatesReferenceSystem))->render();
            }
        }

        $html .= '</section>'.PHP_EOL;

        return $html;
    }

    /**
     * The hierarchy of the presentation
     */
    protected function getNavigation()
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
    protected function properties()
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
        return [
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.openxmlformats-officedocument.presentationml.template',
            'application/vnd.oasis.opendocument.presentation',
        ];
    }

    protected function toClassName($string)
    {
        $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        return strtolower($slug);
    }
}
