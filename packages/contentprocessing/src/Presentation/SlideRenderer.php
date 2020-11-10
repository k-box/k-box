<?php

namespace KBox\Documents\Presentation;

use Illuminate\Support\Str;
use ReflectionClass;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\Table;
use PhpOffice\PhpPresentation\Shape\RichText\BreakElement;
use PhpOffice\PhpPresentation\Shape\RichText\Paragraph;
use PhpOffice\PhpPresentation\Shape\RichText\TextElement;
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

    private $width;

    private $height;

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
    public function __construct(Slide $slide, $index = 0, $coordinates = [0, 0, 960, 540])
    {
        $this->slide = $slide;

        $this->slideIndex = $index;
        
        $this->coordinates = $coordinates;

        $this->width = $coordinates[2];
        $this->height = $coordinates[3];
    }

    public function render()
    {
        $layout = $this->slide->getSlideLayout();

        $background = $this->getBackground();

        $style = [
            $background,
            'width:72rem',
            'height:40rem', // default 16:9 proportion
        ];
        
        $classes = array_filter([
            'relative',
            'text-black',
            is_null($background) ? 'bg-white' : null,
            'mb-4',
            'overflow-hidden',
            'mx-auto',
        ]);

        $master = $layout->getSlideMaster();
        $masterShapeCollection = $master->getShapeCollection();

        $masterPlaceholders = collect($masterShapeCollection)->mapWithKeys(function ($shp) {
            $key = optional($shp->getPlaceholder())->getType() ?? $shp->getHashCode();
            return [$key => $this->renderableShape($shp)];
        });

        $layoutShapeCollection = $this->slide->getSlideLayout()->getShapeCollection();

        $layoutPlaceholders = collect($layoutShapeCollection)->mapWithKeys(function ($shp) {
            $key = optional($shp->getPlaceholder())->getType() ?? $shp->getHashCode();
            return [$key => $this->renderableShape($shp)];
        });

        $slideShapes = collect($this->slide->getShapeCollection())->mapWithKeys(function ($shp) use ($masterPlaceholders, $layoutPlaceholders) {
            $key = optional($shp->getPlaceholder())->getType() ?? $shp->getHashCode();

            $render = null;
            if ($shp->isPlaceholder()) {
                $render = $masterPlaceholders->get($key);
                $render = $this->mergeRenderableShape($render, $layoutPlaceholders->get($key));
                $render = $this->mergeRenderableShape($render, $this->renderableShape($shp));
            } else {
                $render = $this->renderableShape($shp);
            }

            return [$key => $render];
        });

        $slideContentHtml = $masterPlaceholders->except(Placeholders::list())
            ->merge($layoutPlaceholders->except(Placeholders::list()))
            ->merge($slideShapes)
            ->map(function ($value) {
                if ($value['x'] < 0 || $value['y'] < 0) {
                    return null;
                }

                $size = '';
                if($value['width'] > 0 &&  $value['height'] > 0){
                    $size = sprintf('width:%1$spx;height:%2$spx;', $value['width'], $value['height']);
                }

                return sprintf(
                    '<div class="absolute" style="transform:translate(%1$spx, %2$spx);%3$s">%4$s</div>',
                    $value['x'],
                    $value['y'],
                    $size,
                    $value['content']
                );
            })
            ->filter()->values()
            ->join('');

        return sprintf(
            '<div class="%1$s" id="slide-%2$s" style="%3$s">%4$s</div>',
            implode(' ', $classes),
            $this->slideIndex,
            implode(';', $style),
            $slideContentHtml
        );
    }

    // ------------

    private function getBackground()
    {
        $oBkg = $this->slide->getBackground();
        $backgroundDetail = null;
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

    protected function getConstantName($class, $search, $startWith = '')
    {
        $fooClass = new ReflectionClass($class);
        $constants = $fooClass->getConstants();
        $constName = null;
        foreach ($constants as $key => $value) {
            if ($value == $search) {
                if (empty($startWith) || (! empty($startWith) && strpos($key, $startWith) === 0)) {
                    $constName = $key;
                }
                break;
            }
        }
        return $constName;
    }

    protected function renderDrawingGd(Drawing\Gd $shape)
    {
        if (is_null($shape)) {
            return null;
        }
        
        $sShapeImgContents = $shape->getContents();
        
        return '<img class="m-0" src="data:'.$shape->getMimeType().';base64,'.base64_encode($sShapeImgContents).'" width="'.$shape->getWidth().'" height="'.$shape->getHeight().'">';
    }
    
    protected function renderRichText(RichText $shape)
    {
        $style = '';

        $class = '';
        
        $placeholder = null;
        
        if ($shape->isPlaceholder()) {
            if ($shape->getPlaceholder()->getType() === Placeholder::PH_TYPE_TITLE) {
                $class .='text-2xl font-bold';
            };

            if ($shape->getPlaceholder()->getType() === Placeholder::PH_TYPE_BODY) {
                $class .='text-lg';
            };

            if ($shape->getPlaceholder()->getType() === Placeholder::PH_TYPE_FOOTER) {
                $class .='text-sm';
            };
        }

        $padding = 'padding:'.$shape->getInsetTop().'px '.$shape->getInsetRight().'px '.$shape->getInsetBottom().'px '.$shape->getInsetLeft().'px;';
        
        $return = '<div class="'.$class.'" style="'.$style.';'.$padding.'">';

        $paragraphs = $shape->getParagraphs();
        $paragraphCount = count($paragraphs);

        $placeholderParagraphs = is_null($placeholder) ? [] : (array) $placeholder->getParagraphs();

        $placeholderParagraphsCount = count($placeholderParagraphs);

        $oParagraph = null;
        for ($i=0; $i < $paragraphCount; $i++) {
            $oParagraph = $paragraphs[$i];
            
            if ($i < $placeholderParagraphsCount) {
                $return .= $this->renderParagraph($oParagraph, $shape, $placeholderParagraphs[$i]);
            } else {
                $return .= $this->renderParagraph($oParagraph, $shape, null);
            }
        }
                
        $return .='</div>';
        return $return;
    }

    protected function renderParagraph(Paragraph $paragraph, RichText $shape, Paragraph $placeholder = null)
    {
        $class = '';
        $paragraph_style = [];
        $return = '';

        if (method_exists($paragraph, 'getAlignment') && $paragraph->getAlignment()->getHorizontal() === 'ctr') {
            $paragraph_style[] = 'text-align:center';
        }
        
        foreach ($paragraph->getRichTextElements() as $oRichText) {
            $style = [];
            
            if ($oRichText instanceof BreakElement) {
                $return .= '<br/>';
            } else {
                $sub = $oRichText->getFont()->isSubScript();
                $sup = $oRichText->getFont()->isSuperScript();

                $prefix_tag = $sub ? 'sub' : ($sup ? 'sup' : null);

                if ($oRichText->getFont()->getSize() > 10) {
                    $style[] = 'font-size:'.$oRichText->getFont()->getSize().'px';
                }
                if ($oRichText->getFont()->isBold()) {
                    $style[] = 'font-weight:bold';
                }
                if ($oRichText->getFont()->isItalic()) {
                    $style[] = 'font-style:italic';
                }
                if ($oRichText->getFont()->getColor()->getRGB() != '000000') {
                    $style[] = 'color:#'.$oRichText->getFont()->getColor()->getRGB();
                }

                $underline = $oRichText->getFont()->getUnderline();
                if ($underline != Font::UNDERLINE_NONE) {
                    $style[] = 'text-decoration:underline';
                    $style[] = 'text-decoration-style:'.$this->underline_styles[$underline];
                }
                if ($oRichText->getFont()->isStrikethrough()) {
                    $style[] = 'text-decoration:line-through';
                }
                
                if (Str::startsWith($oRichText->getText(), "\t")) {
                    $style[] = 'margin-left:2.5rem;display:inline-block;';
                }

                $innerText = '';

                if (empty($style) && ! ($sub || ! $sup)) {
                    $return .= $oRichText->getText();
                } else {
                    $tag = $sub ? 'sub' : ($sup ? 'sup' : 'span');

                    $anchor='';
                    if ($oRichText instanceof TextElement && $oRichText->hasHyperlink()) {
                        $tag = 'a';
                        $anchor = 'href="'.$oRichText->getHyperlink()->getUrl().'" title="'.$oRichText->getHyperlink()->getTooltip().'"';
                    }

                    $return .= '<'.$tag.' '.$anchor.' style="'.implode(';', $style).'">'.$oRichText->getText().'</'.$tag.'>';
                }
            }
        }

        return sprintf('<p style="%1$s" class="%2$s">%3$s</p>', implode(';', $paragraph_style), $class, $return);
    }

    protected function renderTable(Table $table)
    {
        $tr = [];

        $rows = $table->getRows();
        $cells = null;
        $td = null;

        foreach ($rows as $row) {
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

        if (array_key_exists($layout, $map)) {
            return $map[$layout];
        }

        return Layout::BLANK;
    }

    protected function toClassName($string)
    {
        $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
        return strtolower($slug);
    }

    protected function renderableShape($shape)
    {
        $content = null;

        if ($shape->isPlaceholder() && $shape->getPlaceholder()->getType() === Placeholder::PH_TYPE_SLIDENUM) {
            $content = $this->slideIndex;
        }

        if ($shape instanceof Drawing\Gd) {
            $content = $this->renderDrawingGd($shape);
        } elseif ($shape instanceof RichText && ! ($shape->isPlaceholder() && $shape->getPlaceholder()->getType() === Placeholder::PH_TYPE_SLIDENUM)) {
            $content = $this->renderRichText($shape);
        } elseif ($shape instanceof Table) {
            $content = $this->renderTable($shape);
        }

        return [
            'container' => $shape->getContainer() ? [$shape->getContainer()->getOffsetX(), $shape->getContainer()->getOffsetY(), $shape->getContainer()->getExtentX(), $shape->getContainer()->getExtentY()] : null,
            'x' => $shape->getOffsetX(),
            'y' => $shape->getOffsetY(),
            'width' => $shape->getWidth(),
            'height' => $shape->getHeight(),
            'rotation' => $shape->getRotation(),
            'content' => $content,

            'fill' => $shape->getFill(),
            'border' => $shape->getBorder(),
            'shadow' => $shape->getShadow(),
            'hyperlink' => $shape->getHyperlink(),
        ];
    }

    protected function mergeRenderableShape($shape1, $shape2)
    {
        if (is_null($shape1)) {
            return $shape2;
        }
        if (is_null($shape2)) {
            return $shape1;
        }
        
        return [
            'container' => null, // $shape1['container'] .'-'. $shape2['container'],
            'x' => max($shape1['x'], $shape2['x']),
            'y' => max($shape1['y'], $shape2['y']),
            'width' => max($shape1['width'], $shape2['width']),
            'height' => max($shape1['height'], $shape2['height']),
            'rotation' => max($shape1['rotation'], $shape2['rotation']),
            'content' => $shape2['content'],

            'fill' => $shape2['fill'],
            'border' => $shape2['border'],
            'shadow' => $shape2['shadow'],
            'hyperlink' => $shape2['hyperlink'],
        ];
    }
}
