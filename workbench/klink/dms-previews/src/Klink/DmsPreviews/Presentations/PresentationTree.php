<?php

namespace Klink\DmsPreviews\Presentations;

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\AbstractShape;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\MemoryDrawing;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\BreakElement;
use PhpOffice\PhpPresentation\Shape\RichText\TextElement;
use PhpOffice\PhpPresentation\Shape\RichText\Run;
use PhpOffice\PhpPresentation\Shape\RichText\Paragraph;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;
use ReflectionClass;


class PresentationTree {
    
    // Internals are currently copied from https://raw.githubusercontent.com/PHPOffice/PHPPresentation/86cfd33bb8f171d174ffc70f3b13f3ab38a765a5/samples/Sample_Header.php
    
    protected $oPhpPresentation;
    protected $htmlOutput;

    public function __construct(PhpPresentation $oPHPPpt)
    {
        $this->oPhpPresentation = $oPHPPpt;
    }

    public function display()
    {
        // $this->append('<div class="container-fluid pptTree">');
        // $this->append('<div class="row">');
        // $this->append('<div class="collapse in col-md-6">');
        $this->append('<div class="presentation row">');
        
        $this->displayPhpPresentation($this->oPhpPresentation);
        
        $this->append('</div>');

        // $this->displayPhpPresentationInfo($this->oPhpPresentation);


        return $this->htmlOutput;
    }

    protected function append($sHTML)
    {
        $this->htmlOutput .= $sHTML;
    }

    protected function displayPhpPresentation(PhpPresentation $oPHPPpt)
    {


        // Get the document layout to establish the proportion of the slide
        $layout = $oPHPPpt->getLayout()->getDocumentLayout();

        $slide_counter = 1;
        
        foreach ($oPHPPpt->getAllSlides() as $oSlide) {
            
            // TODO: what about getBackground() : PhpOffice\PhpPresentation\Slide\AbstractBackground

            // TODO: Master layout is what tell how to render the slide, 
            // but the Pull request for getting the master slide layout is not merged 
            // https://github.com/PHPOffice/PHPPresentation/pull/230, 
            // see issue https://github.com/PHPOffice/PHPPresentation/issues/185 and 
            // https://github.com/PHPOffice/PHPPresentation/issues/161

            $this->append('<div id="slide'.$slide_counter.'.xml" class="slide slide__layout-'.$layout.'" data-name="'. $oSlide->getName() .'" >');
            
            // $this->append('<span class="slide__meta">'.$oSlide->getHashCode().' - name: <strong>'. $oSlide->getName() .'</strong>, master: <strong>'. $oSlide->getSlideMasterId() .'</strong>, layout: <strong>'.str_slug($oSlide->getSlideLayout()).'</strong></span>');

                $this->append('<div class="slide__content slide__master-'. str_slug($oSlide->getSlideLayout()) .'">');

                foreach ($oSlide->getShapeCollection() as $oShape) {

                    if($oShape instanceof Group) {
                        
                        foreach ($oShape->getShapeCollection() as $oShapeChild) {
                            $this->displayShape($oShapeChild);
                        }

                    } else {
                        $this->displayShape($oShape);
                    }
                }

                $this->append('</div>');
            
            $this->append('</div>');

            $slide_counter++;
        }
        
    }

    protected function displayShape(AbstractShape $shape)
    {
        if($shape instanceof Drawing\Gd) {
            $this->append('<span class="shape" id="div'.$shape->getHashCode().'">Shape "Drawing\Gd"</span>');
        } elseif($shape instanceof Drawing\File) {
            $this->append('<span class="shape" id="div'.$shape->getHashCode().'">Shape "Drawing\File"</span>');
        } elseif($shape instanceof Drawing\Base64) {
            $this->append('<span class="shape" id="div'.$shape->getHashCode().'">Shape "Drawing\Base64"</span>');
        } elseif($shape instanceof Drawing\Zip) {
            $this->append('<span class="shape" id="div'.$shape->getHashCode().'">Shape "Drawing\Zip"</span>');
        } elseif($shape instanceof RichText) {
            // $this->append('<span class="shape" id="div'.$shape->getHashCode().'">Shape "RichText"</span>');
            // var_dump($shape->getPlainText());
            $this->displayTextShape($shape);
        } /*else {
            var_dump($shape);
        }*/
        // $this->displayShapeInfo($shape);
    }


    protected function displayTextShape(RichText $shape){

        $paragraphs = $shape->getParagraphs(); // a RichText shape cotains only Paragraphs and each Paragraph contains elements of TextElementInterface

        $paragraphs = array_filter(array_flatten( $paragraphs));

        

        $bullet_type = 'TYPE_NONE';

        foreach ($paragraphs as $paragraph) {

            if(!empty($paragraph->getRichTextElements())){

                $bullet_type = $this->getConstantName('\PhpOffice\PhpPresentation\Style\Bullet', $paragraph->getBulletStyle()->getBulletType());

                if($bullet_type==='TYPE_NONE'){
                    $this->append('<p>');
                }
                else {
                    $this->append('<p>');
                }


                foreach ($paragraph->getRichTextElements() as $textElement) {
                            
                    if($textElement instanceof BreakElement) {

                        $this->append('<div>&nbsp;</div>');

                    } else if ($textElement instanceof TextElement) {

                        // $this->append('<code>TextElement</code>');

                        $text = '<span>';

                        if($textElement->getFont()->isBold()){
                            $text .= '<strong>';
                        }

                        if($textElement->getFont()->isItalic()){
                            $text .= '<em>';
                        }


                        $text .= $textElement->getText();


                        if($textElement->getFont()->isBold()){
                            $text .= '</strong>';
                        }

                        if($textElement->getFont()->isItalic()){
                            $text .= '</em>';
                        }

                        $text .= '</span>';
                        
                        if ($textElement->hasHyperlink()) {
                            $this->append('<a href="#'.$textElement->getHyperlink()->getUrl().'" title="'.$textElement->getHyperlink()->getTooltip().'">'.$text.'</a>');
                        }
                        else {
                            $this->append($text);
                        }
                            
                    } else {
                        $this->append($textElement->getText());

                    }
                        
                }

                if($bullet_type==='TYPE_NONE'){
                    $this->append('<p>');
                }
                else {
                    $this->append('<p>');
                }

            }

        }

    }

    protected function displayPhpPresentationInfo(PhpPresentation $oPHPPpt)
    {
        $this->append('<div class="infoBlk" id="divPhpPresentationInfo">');
        $this->append('<dl>');
        $this->append('<dt>Number of slides</dt><dd>'.$oPHPPpt->getSlideCount().'</dd>');
        $this->append('<dt>Document Layout Height</dt><dd>'.$oPHPPpt->getLayout()->getCY(DocumentLayout::UNIT_MILLIMETER).' mm</dd>');
        $this->append('<dt>Document Layout Width</dt><dd>'.$oPHPPpt->getLayout()->getCX(DocumentLayout::UNIT_MILLIMETER).' mm</dd>');
        $this->append('<dt>Properties : Category</dt><dd>'.$oPHPPpt->getProperties()->getCategory().'</dd>');
        $this->append('<dt>Properties : Company</dt><dd>'.$oPHPPpt->getProperties()->getCompany().'</dd>');
        $this->append('<dt>Properties : Created</dt><dd>'.$oPHPPpt->getProperties()->getCreated().'</dd>');
        $this->append('<dt>Properties : Creator</dt><dd>'.$oPHPPpt->getProperties()->getCreator().'</dd>');
        $this->append('<dt>Properties : Description</dt><dd>'.$oPHPPpt->getProperties()->getDescription().'</dd>');
        $this->append('<dt>Properties : Keywords</dt><dd>'.$oPHPPpt->getProperties()->getKeywords().'</dd>');
        $this->append('<dt>Properties : Last Modified By</dt><dd>'.$oPHPPpt->getProperties()->getLastModifiedBy().'</dd>');
        $this->append('<dt>Properties : Modified</dt><dd>'.$oPHPPpt->getProperties()->getModified().'</dd>');
        $this->append('<dt>Properties : Subject</dt><dd>'.$oPHPPpt->getProperties()->getSubject().'</dd>');
        $this->append('<dt>Properties : Title</dt><dd>'.$oPHPPpt->getProperties()->getTitle().'</dd>');
        $this->append('</dl>');
        $this->append('</div>');

        foreach ($oPHPPpt->getAllSlides() as $oSlide) {
            $this->append('<div class="infoBlk" id="div'.$oSlide->getHashCode().'Info">');
            $this->append('<dl>');
            $this->append('<dt>HashCode</dt><dd>'.$oSlide->getHashCode().'</dd>');
            $this->append('<dt>Slide Layout</dt><dd>Layout::'.$this->getConstantName('\PhpOffice\PhpPresentation\Slide\Layout', $oSlide->getSlideLayout()).'</dd>');
            
            $this->append('<dt>Offset X</dt><dd>'.$oSlide->getOffsetX().'</dd>');
            $this->append('<dt>Offset Y</dt><dd>'.$oSlide->getOffsetY().'</dd>');
            $this->append('<dt>Extent X</dt><dd>'.$oSlide->getExtentX().'</dd>');
            $this->append('<dt>Extent Y</dt><dd>'.$oSlide->getExtentY().'</dd>');
            $oBkg = $oSlide->getBackground();
            if ($oBkg instanceof Slide\AbstractBackground) {
                if ($oBkg instanceof Slide\Background\Color) {
                    $this->append('<dt>Background Color</dt><dd>#'.$oBkg->getColor()->getRGB().'</dd>');
                }
                if ($oBkg instanceof Slide\Background\Image) {
                    $sBkgImgContents = file_get_contents($oBkg->getPath());
                    $this->append('<dt>Background Image</dt><dd><img src="data:image/png;base64,'.base64_encode($sBkgImgContents).'"></dd>');
                }
            }
            $this->append('</dl>');
            $this->append('</div>');

            foreach ($oSlide->getShapeCollection() as $oShape) {
                if($oShape instanceof Group) {
                    foreach ($oShape->getShapeCollection() as $oShapeChild) {
                        $this->displayShapeInfo($oShapeChild);
                    }
                } else {
                    $this->displayShapeInfo($oShape);
                }
            }
        }
    }

    protected function displayShapeInfo(AbstractShape $oShape)
    {
        $this->append('<div class="infoBlk" id="div'.$oShape->getHashCode().'Info">');
        $this->append('<dl>');
        $this->append('<dt>HashCode</dt><dd>'.$oShape->getHashCode().'</dd>');
        $this->append('<dt>Offset X</dt><dd>'.$oShape->getOffsetX().'</dd>');
        $this->append('<dt>Offset Y</dt><dd>'.$oShape->getOffsetY().'</dd>');
        $this->append('<dt>Height</dt><dd>'.$oShape->getHeight().'</dd>');
        $this->append('<dt>Width</dt><dd>'.$oShape->getWidth().'</dd>');
        $this->append('<dt>Rotation</dt><dd>'.$oShape->getRotation().'°</dd>');
        $this->append('<dt>Hyperlink</dt><dd>'.ucfirst(var_export($oShape->hasHyperlink(), true)).'</dd>');
        $this->append('<dt>Fill</dt><dd>@Todo</dd>');
        $this->append('<dt>Border</dt><dd>@Todo</dd>');
        if($oShape instanceof MemoryDrawing) {
            $this->append('<dt>Name</dt><dd>'.$oShape->getName().'</dd>');
            $this->append('<dt>Description</dt><dd>'.$oShape->getDescription().'</dd>');
            ob_start();
            call_user_func($oShape->getRenderingFunction(), $oShape->getImageResource());
            $sShapeImgContents = ob_get_contents();
            ob_end_clean();
            $this->append('<dt>Mime-Type</dt><dd>'.$oShape->getMimeType().'</dd>');
            $this->append('<dt>Image</dt><dd><img src="data:'.$oShape->getMimeType().';base64,'.base64_encode($sShapeImgContents).'"></dd>');
        } elseif($oShape instanceof Drawing) {
            $this->append('<dt>Name</dt><dd>'.$oShape->getName().'</dd>');
            $this->append('<dt>Description</dt><dd>'.$oShape->getDescription().'</dd>');
        } elseif($oShape instanceof RichText) {
            $this->append('<dt># of paragraphs</dt><dd>'.count($oShape->getParagraphs()).'</dd>');
            $this->append('<dt>Inset (T / R / B / L)</dt><dd>'.$oShape->getInsetTop().'px / '.$oShape->getInsetRight().'px / '.$oShape->getInsetBottom().'px / '.$oShape->getInsetLeft().'px</dd>');
            $this->append('<dt>Text</dt>');
            $this->append('<dd>');
            foreach ($oShape->getParagraphs() as $oParagraph) {
                $this->append('Paragraph<dl>');
                $this->append('<dt>Alignment Horizontal</dt><dd> Alignment::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $oParagraph->getAlignment()->getHorizontal()).'</dd>');
                $this->append('<dt>Alignment Vertical</dt><dd> Alignment::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $oParagraph->getAlignment()->getVertical()).'</dd>');
                $this->append('<dt>Alignment Margin (L / R)</dt><dd>'.$oParagraph->getAlignment()->getMarginLeft().' px / '.$oParagraph->getAlignment()->getMarginRight().'px</dd>');
                $this->append('<dt>Alignment Indent</dt><dd>'.$oParagraph->getAlignment()->getIndent().' px</dd>');
                $this->append('<dt>Alignment Level</dt><dd>'.$oParagraph->getAlignment()->getLevel().'</dd>');
                $this->append('<dt>Bullet Style</dt><dd> Bullet::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Bullet', $oParagraph->getBulletStyle()->getBulletType()).'</dd>');
                $this->append('<dt>Bullet Font</dt><dd>'.$oParagraph->getBulletStyle()->getBulletFont().'</dd>');
                if ($oParagraph->getBulletStyle()->getBulletType() == Bullet::TYPE_BULLET) {
                    $this->append('<dt>Bullet Char</dt><dd>'.$oParagraph->getBulletStyle()->getBulletChar().'</dd>');
                }
                if ($oParagraph->getBulletStyle()->getBulletType() == Bullet::TYPE_NUMERIC) {
                    $this->append('<dt>Bullet Start At</dt><dd>'.$oParagraph->getBulletStyle()->getBulletNumericStartAt().'</dd>');
                    $this->append('<dt>Bullet Style</dt><dd>'.$oParagraph->getBulletStyle()->getBulletNumericStyle().'</dd>');
                }
                $this->append('<dt>RichText</dt><dd><dl>');
                foreach ($oParagraph->getRichTextElements() as $oRichText) {
                    if($oRichText instanceof BreakElement) {
                        $this->append('<dt><i>Break</i></dt>');
                    } else {
                        if ($oRichText instanceof TextElement) {
                           $this->append('<dt><i>TextElement</i></dt>');
                        } else {
                           $this->append('<dt><i>Run</i></dt>');
                        }
                        $this->append('<dd>'.$oRichText->getText());
                        $this->append('<dl>');
                        $this->append('<dt>Font Name</dt><dd>'.$oRichText->getFont()->getName().'</dd>');
                        $this->append('<dt>Font Size</dt><dd>'.$oRichText->getFont()->getSize().'</dd>');
                        $this->append('<dt>Font Color</dt><dd>#'.$oRichText->getFont()->getColor()->getARGB().'</dd>');
                        $this->append('<dt>Font Transform</dt><dd>');
                            $this->append('<abbr title="Bold">Bold</abbr> : '.($oRichText->getFont()->isBold() ? 'Y' : 'N').' - ');
                            $this->append('<abbr title="Italic">Italic</abbr> : '.($oRichText->getFont()->isItalic() ? 'Y' : 'N').' - ');
                            $this->append('<abbr title="Underline">Underline</abbr> : Underline::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Font', $oRichText->getFont()->getUnderline()).' - ');
                            $this->append('<abbr title="Strikethrough">Strikethrough</abbr> : '.($oRichText->getFont()->isStrikethrough() ? 'Y' : 'N').' - ');
                            $this->append('<abbr title="SubScript">SubScript</abbr> : '.($oRichText->getFont()->isSubScript() ? 'Y' : 'N').' - ');
                            $this->append('<abbr title="SuperScript">SuperScript</abbr> : '.($oRichText->getFont()->isSuperScript() ? 'Y' : 'N'));
                        $this->append('</dd>');
                        if ($oRichText instanceof TextElement) {
                            if ($oRichText->hasHyperlink()) {
                                $this->append('<dt>Hyperlink URL</dt><dd>'.$oRichText->getHyperlink()->getUrl().'</dd>');
                                $this->append('<dt>Hyperlink Tooltip</dt><dd>'.$oRichText->getHyperlink()->getTooltip().'</dd>');
                            }
                        }
                        $this->append('</dl>');
                        $this->append('</dd>');
                    }
                }
                $this->append('</dl></dd></dl>');
            }
            $this->append('</dd>');
        } else {
            // Add another shape
        }
        $this->append('</dl>');
        $this->append('</div>');
    }
    
    protected function getConstantName($class, $search, $startWith = '') {
        $fooClass = new ReflectionClass($class);
        $constants = $fooClass->getConstants();
        $constName = null;
        foreach ($constants as $key => $value ) {
            if ($value == $search) {
                if (empty($startWith) || (!empty($startWith) && strpos($key, $startWith) === 0)) {
                    $constName = $key;
                }
                break;
            }
        }
        return $constName;
    }
}