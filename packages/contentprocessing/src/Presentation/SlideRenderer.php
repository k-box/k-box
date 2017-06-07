<?php

namespace Content\Presentation;

use ReflectionClass;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Settings;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\AbstractShape;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\Group;
use PhpOffice\PhpPresentation\Shape\Table;
use PhpOffice\PhpPresentation\Shape\RichText\BreakElement;
use PhpOffice\PhpPresentation\Shape\RichText\Paragraph;
use PhpOffice\PhpPresentation\Shape\RichText\TextElement;
use PhpOffice\PhpPresentation\Shape\RichText\Run;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Bullet;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Slide\Layout;
use PhpOffice\PhpPresentation\Style\Font;
use PhpOffice\PhpPresentation\Shape\Placeholder;

/**
 * 
 */
class SlideRenderer
{

    /**
     * @var Slide
     */
    private $slide = null;

    /**
     * @var int
     */
    private $slideIndex = 0;

    private $underline_styles = [
        Font::UNDERLINE_DASH => 'dashed',
        Font::UNDERLINE_DASHHEAVY => 'dashed',
        Font::UNDERLINE_DASHLONG => 'dashed',
        Font::UNDERLINE_DASHLONGHEAVY => 'dashed',
        Font::UNDERLINE_DOUBLE => 'double',
        Font::UNDERLINE_DOTHASH => 'dotted',
        Font::UNDERLINE_DOTHASHHEAVY => 'dotted',
        Font::UNDERLINE_DOTDOTDASH => 'dashed',
        Font::UNDERLINE_DOTDOTDASHHEAVY => 'dashed',
        Font::UNDERLINE_DOTTED => 'dotted',
        Font::UNDERLINE_DOTTEDHEAVY => 'dotted',
        Font::UNDERLINE_HEAVY => 'solid',
        Font::UNDERLINE_WAVY => 'wavy',
        Font::UNDERLINE_WAVYDOUBLE => 'wavy',
        Font::UNDERLINE_WAVYHEAVY => 'wavy',
        Font::UNDERLINE_WORDS => 'solid',
        Font::UNDERLINE_SINGLE => 'solid',
    ];

    /**
     *
     * @param int $index the index of the slide in all the presentation
     */
    public function __construct(Slide $slide, $index = 0)
    {
        $this->slide = $slide;

        $this->slideIndex = $index;
    }



    public function render()
    {

        $background = $this->getBackground();
            
        $slide_layout_class = 'slide-'. $this->toClassName($this->normalizeSlideLayout($this->slide->getSlideLayout()->getLayoutName()));

        $style = $background;

        if($this->slide->getExtentX() > 0)
        {
            $style .= ';width:'.$this->slide->getExtentX().'px';
        }
        
        if($this->slide->getExtentY() > 0)
        {
            $style .= ';height:'.$this->slide->getExtentY().'px';
        }

        $html = '<div class="slide '.$slide_layout_class.'" id="slide'. $this->slideIndex .'" style="'.$style.'">' . PHP_EOL;
            // $html .= '<div class="slide__info">';
            // $html .= '<span class="slide__infoitem">Slide '.$this->slideIndex.'</span>';
            // // il layout nei file creati con Power point in Italiano è scritto in italiano
            // // quindi va normalizzato alla versione inglese per avere una corretta rappresentazione
            // $html .= '<span class="slide__infoitem">Layout '.$slide->getSlideLayout()->getLayoutName().' <code>'. $slide_layout_class.'</code></span>';
            // $html .= '<span class="slide__infoitem">Offset X '.$slide->getOffsetX().'</span>';
            // $html .= '<span class="slide__infoitem">Offset Y '.$slide->getOffsetY().'</span>';
            // $html .= '<span class="slide__infoitem">Extent X '.$slide->getExtentX().'</span>';
            // $html .= '<span class="slide__infoitem">Extent Y '.$slide->getExtentY().'</span>';
            // $html .= '<span class="slide__infoitem">Background '.$backgroundDetail.'</span>';
            // $html .= '</div>';

        $layoutShapeCollection = $this->slide->getSlideLayout()->getShapeCollection();

        foreach ($layoutShapeCollection as $oShape) {
            
            if(!$oShape->isPlaceholder() && !($oShape instanceof RichText)){


                if($oShape instanceof Group) {
                    foreach ($oShape->getShapeCollection() as $oShapeChild) {
                        $html .= $this->displayShapeInfo($oShapeChild);
                    }
                } else {
                    $html .= $this->displayShapeInfo($oShape);
                }
            }
        }

        foreach ($this->slide->getShapeCollection() as $oShape) {
            
            if($oShape instanceof Group) {
                foreach ($oShape->getShapeCollection() as $oShapeChild) {
                    $html .= $this->displayShapeInfo($oShapeChild);
                }
            } else {
                $html .= $this->displayShapeInfo($oShape);
            }
        }
        $html .= '</div>' . PHP_EOL;

        return $html;
    }


    // ------------

    private function getMasterLayout()
    {
        // $slideMasterId = $this->slide->getSlideMasterId();

        $master = $this->slide->getParent()->getAllMasterSlides()[0];

        // $textStyles = $master->getTextStyles();
        // $colorScheme = $master->getAllSchemeColors();

        // $layouts =  $master->getAllSlideLayouts();

        // TODO: find the same layout of the current slide



        // var_dump($master->getBackground());
        // var_dump($this->slide->getSlideLayout()->getShapeCollection());

    }

    private function getPlaceholder($type)
    {
        $layoutShapeCollection = $this->slide->getSlideLayout()->getShapeCollection();



        $filtered = array_values(array_filter((array) $layoutShapeCollection, function($shp) use ($type)
        {
            return $shp->isPlaceholder() && $shp->getPlaceholder()->getType() == $type;
        }));

        if(count($filtered) >= 1)
        {
            return $filtered[0];
        }

        return null;
    }

    private function getBackground()
    {
        // TODO: check the master slide of the layout for additional background info

        $oBkg = $this->slide->getBackground();
        $backgroundDetail = '';
        if ($oBkg instanceof Slide\AbstractBackground) {
            if ($oBkg instanceof Slide\Background\Color) {
                $backgroundDetail = 'background:#'.$oBkg->getColor()->getRGB().';';
            }
            if ($oBkg instanceof Slide\Background\Image) {
                $sBkgImgContents = file_get_contents($oBkg->getPath());
                $backgroundDetail = 'background:url("data:image/png;base64,'.base64_encode($sBkgImgContents).'");';
            }
        }

        return $backgroundDetail;
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

    protected function displayShapeInfo(AbstractShape $oShape)
    {
        $return = '';
        

        if($oShape instanceof Drawing\Gd) {
            $return .= $this->renderDrawingGd($oShape);
        } elseif($oShape instanceof Drawing) {
            $return .= $this->renderDrawing($oShape);
            
        } elseif($oShape instanceof RichText) {
            $return .= $this->renderRichText($oShape);
        } elseif($oShape instanceof Table) {
            $return .= $this->renderTable($oShape);
        } else {
            // Add another shape
            $return .= '<span>Unknown shape '. get_class($oShape) .'</span>';
        }

        return $return;
    }



    protected function renderDrawingGd(Drawing\Gd $shape)
    {
        if(is_null($shape))
        {
            return '';
        }
        // $return = '<span>Name '.$shape->getName().'</span>';
        // $return .= '<span>Description '.$shape->getDescription().'</span>';
        ob_start();
        call_user_func($shape->getRenderingFunction(), $shape->getImageResource());
        $sShapeImgContents = ob_get_contents();
        ob_end_clean();

        // $return = '<div class="shape__info">';
        // $return .= '<span>Name '.$shape->getName().'</span>';
        // $return .= '<span>Description '.$shape->getDescription().'</span>';
        // $return .= '<span>'. get_class($shape) .'</span>';
        // $return .= '<span>Offset X '.$shape->getOffsetX().'</span>';
        // $return .= '<span>Offset Y '.$shape->getOffsetY().'</span>';
        // $return .= '<span>Height '.$shape->getHeight().'</span>';
        // $return .= '<span>Width '.$shape->getWidth().'</span>';
        // $return .= '<span>Rotation '.$shape->getRotation().'°</span>';
        // $return .= '<span>Hyperlink '.ucfirst(var_export($shape->hasHyperlink(), true)).'</span>';
        // $return .= '<span>IsPlaceholder ' . ($shape->isPlaceholder() ? 'true' : 'false') . '</span>';
        // $return .= '</div>';

        $style = 'position:absolute;z-index:0;left:' . $shape->getOffsetX() .'px;top:'.$shape->getOffsetY().'px;';

        // $return .= '<span>Mime-Type '.$shape->getMimeType().'</span>';
        $return = '<img src="data:'.$shape->getMimeType().';base64,'.base64_encode($sShapeImgContents).'" style="'.$style.'" width="'.$shape->getWidth().'" height="'.$shape->getHeight().'">';
        return $return;
    }

    protected function renderDrawing(Drawing $shape)
    {
        $return = '<span>Name '.$shape->getName().'</span>';
        $return .= '<span>Description '.$shape->getDescription().'</span>';
        return $return;
    }
    
    protected function renderRichText(RichText $shape)
    {

        // $shape->isPlaceholder(); // indicates that the slide layout has some options for this shape

        // $tooltip = '# of paragraphs '.count($shape->getParagraphs()).PHP_EOL.'class ' . get_class($shape);

        $style = '';

        if($shape->getOffsetX() > 0 || $shape->getOffsetY() > 0)
        {
            $style .= 'position:absolute;z-index:1;left:' . $shape->getOffsetX() .'px;top:'.$shape->getOffsetY().'px;';
        }

        if($shape->getWidth() > 0 && $shape->getHeight() > 0)
        {
            $style .= 'width:' . $shape->getWidth().'px;height:'.$shape->getHeight().'px';
        }

        $class = 'shape';
        $placeholder_info='';
        $placeholder = null;
        if($shape->isPlaceholder())
        {
            $class .= ' shape-'.$this->toClassName($shape->getPlaceholder()->getType());

            $placeholder = $this->getPlaceholder($shape->getPlaceholder()->getType());

            $style = '';

            if($placeholder->getOffsetX() > 0 || $placeholder->getOffsetY() > 0)
            {
                $style .= 'position:absolute;z-index:1;left:' . $placeholder->getOffsetX() .'px;top:'.$placeholder->getOffsetY().'px;';
            }

            if($placeholder->getWidth() > 0 && $placeholder->getHeight() > 0)
            {
                $style .= 'width:' . $placeholder->getWidth().'px;height:'.$placeholder->getHeight().'px';
            }
    

            // $tooltip .= ' placeholder: '.$shape->getPlaceholder()->getType();

            if($shape->getPlaceholder()->getType() == Placeholder::PH_TYPE_SLIDENUM)
            {
                return '<div class="slide__number">'. $this->slideIndex .'</div>';
            }

            // the rich text element has paragraphs and so they need to be explored
            // $placeholder_info = '<span>Alignment Horizontal  Alignment::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $placeholder->getAlignment()->getHorizontal()).'</span>';
            // $placeholder_info .= '<span>Alignment Vertical  Alignment::'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $placeholder->getAlignment()->getVertical()).'</span>';
        }

        // var_dump($shape->getFill());

        $padding = 'padding:' .$shape->getInsetTop().'px '.$shape->getInsetRight().'px '.$shape->getInsetBottom().'px '.$shape->getInsetLeft().'px;';
        
        $return = '<div class="'.$class.'" style="'.$style.';'.$padding.'">';

        $paragraphs = $shape->getParagraphs();
        $paragraphCount = count($paragraphs);

        $placeholderParagraphs = is_null($placeholder) ? [] : (array) $placeholder->getParagraphs();

        $placeholderParagraphsCount = count($placeholderParagraphs);

        $oParagraph = null;
        for ($i=0; $i < $paragraphCount; $i++)
        { 
            $oParagraph = $paragraphs[$i];
            if($i < $placeholderParagraphsCount){
                $return .= $this->renderParagraph($oParagraph, $shape, $placeholderParagraphs[$i]);
            }
            else 
            {
                $return .= $this->renderParagraph($oParagraph, $shape, null);
            }
        }
                
        $return .='</div>';
        return $return;
    }


    protected function renderParagraph(Paragraph $paragraph, RichText $shape, Paragraph $placeholder = null)
    {

        $class = 'slide__paragraph';
        $style = [];

        $return = '';
                // $return .= '<abbr title="Alignment Horizontal">'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $paragraph->getAlignment()->getHorizontal()).'</abbr>';
                // $return .= '<abbr title="Alignment Vertical">'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $paragraph->getAlignment()->getVertical()).'</abbr>';
                // if(!is_null($placeholder))
                // {
                //     $return .= '<abbr title="Alignment Horizontal">'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $placeholder->getAlignment()->getHorizontal()).'</abbr>';
                //     $return .= '<abbr title="Alignment Vertical">'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Alignment', $placeholder->getAlignment()->getVertical()).'</abbr>';
                // }
                // // $return .= $placeholder_info;
                // $return .= '<span>Alignment Margin (L / R) '.$oParagraph->getAlignment()->getMarginLeft().' px / '.$oParagraph->getAlignment()->getMarginRight().'px</span>';
                // $return .= '<abbr title="Indent">'.$paragraph->getAlignment()->getIndent().' px</abbr>';
                // $return .= '<abbr title="Alignment level">'.$paragraph->getAlignment()->getLevel().'</abbr>';
                // $return .= '<abbr title="Bullet Style">'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Bullet', $paragraph->getBulletStyle()->getBulletType()).'</abbr>';
                // if(!is_null($placeholder))
                // {
                //     $return .= '<abbr title="Bullet Style">'.$this->getConstantName('\PhpOffice\PhpPresentation\Style\Bullet', $placeholder->getBulletStyle()->getBulletType()).'</abbr>';
                // }
                // if ($paragraph->getBulletStyle()->getBulletType() != Bullet::TYPE_NONE) {
                //     $return .= $paragraph->getBulletStyle()->getBulletType() == Bullet::TYPE_NUMERIC ? '<ol>' : '<ul>';
                // }
                foreach ($paragraph->getRichTextElements() as $oRichText) {

                    $style = [];
                    
                    if($oRichText instanceof BreakElement) {
                        $return .= '<br/>';
                    } else {

                        // if ($paragraph->getBulletStyle()->getBulletType() != Bullet::TYPE_NONE)
                        // {
                        //     $return .= '<li>';
                        // }

                        $sub = $oRichText->getFont()->isSubScript();
                        $sup = $oRichText->getFont()->isSuperScript();

                        $prefix_tag = $sub ? 'sub' : ($sup ? 'sup' : null);

                        
                        if($oRichText->getFont()->getSize() > 10)
                        {
                            $style[] = 'font-size:' . $oRichText->getFont()->getSize() .'px';
                        }
                        if($oRichText->getFont()->isBold())
                        {
                            $style[] = 'font-weight:bold';
                        }
                        if($oRichText->getFont()->isItalic())
                        {
                            $style[] = 'font-style:italic';
                        }
                        if($oRichText->getFont()->getColor()->getRGB() != '000000')
                        {
                            $style[] = 'color:#' . $oRichText->getFont()->getColor()->getRGB();
                        }

                        $underline = $oRichText->getFont()->getUnderline();
                        if($underline != Font::UNDERLINE_NONE)
                        {
                            $style[] = 'text-decoration:underline';
                            $style[] = 'text-decoration-style:' . $this->underline_styles[$underline];

                        }
                        if($oRichText->getFont()->isStrikethrough())
                        {
                            $style[] = 'text-decoration:line-through';
                        }

                        // if ($paragraph->getBulletStyle()->getBulletType() != Bullet::TYPE_NONE)
                        // {
                        //     $return .= $paragraph->getBulletStyle()->getBulletChar();
                        // }

                        $innerText = '';

                        if(empty($style) && !($sub || !$sup))
                        {

                            $return .= $oRichText->getText();
                        }
                        else 
                        {

                            $tag = $sub ? 'sub' : ($sup ? 'sup' : 'span');

                            $anchor='';
                            if($oRichText instanceof TextElement && $oRichText->hasHyperlink())
                            {
                                $tag = 'a';
                                $anchor = 'href="'.$oRichText->getHyperlink()->getUrl().'" title="'.$oRichText->getHyperlink()->getTooltip().'"';
                            }

                            $return .= '<'.$tag.' '.$anchor.' style="' . implode(';', $style) . '">' . $oRichText->getText() . '</'.$tag.'>';
                        }

                        // Sub, Sup and anchor output

                        
                        // if ($paragraph->getBulletStyle()->getBulletType() != Bullet::TYPE_NONE)
                        // {
                        //     $return .= '</li>';
                        // }
                    }
                }
                // if ($paragraph->getBulletStyle()->getBulletType() != Bullet::TYPE_NONE)
                // {
                //     $return .= $paragraph->getBulletStyle()->getBulletType() == Bullet::TYPE_NUMERIC ? '</ol>' : '</ul>';
                // }
        return sprintf('<p style="%1$s" class="%2$s">%3$s</p>', implode(';', $style), $class, $return);
    }

    protected function renderTable(Table $table)
    {


        $tr = [];

        $rows = $table->getRows();
        $cells = null;
        $td = null;

        foreach ($rows as $row) {
            # code...
            $cells = $row->getCells();
            $td = [];

            foreach ($cells as $cell) {
                $td[] = '<td>'.$cell->getPlainText().'</td>';
            }


            $tr[] = sprintf('<tr>%1$s</tr>', implode('', $td));
        }

        return sprintf('<table class="shape">%1$s</table>', implode('', $tr));
    }


    protected function normalizeSlideLayout($layout)
    {
        $map = [
            'Titolo e contenuto' => Layout::TITLE_AND_CONTENT,
            'titolo' => Layout::TITLE_SLIDE,
            'Intestazione sezione' => Layout::SECTION_HEADER,
        ];

        if(array_key_exists($layout, $map)){
            return $map[$layout];
        }

        return Layout::BLANK;
    }

    protected function toClassName($string)
    {
        $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        return strtolower( $slug );
    }

}