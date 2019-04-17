<?php

namespace KBox\Documents\Presentation\Reader;

use PhpOffice\PhpPresentation\Reader\PowerPoint2007 as OriginalPowerPoint2007Reader;

use PhpOffice\Common\XMLReader;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Slide\AbstractSlide;

/**
 * Power point XML file reader
 */
class PowerPoint2007 extends OriginalPowerPoint2007Reader
{
    protected function loadSlide($sPart, $baseFile)
    {
        $xmlReader = new XMLReader();
        if ($xmlReader->getDomFromString($sPart)) {
            // Core
            $oSlide = $this->oPhpPresentation->createSlide();
            $this->oPhpPresentation->setActiveSlideIndex($this->oPhpPresentation->getSlideCount() - 1);
            $oSlide->setRelsIndex('ppt/slides/_rels/'.$baseFile.'.rels');

            // Background
            $oElement = $xmlReader->getElement('/p:sld/p:cSld/p:bg/p:bgPr');
            if ($oElement) {
                $oElementColor = $xmlReader->getElement('a:solidFill/a:srgbClr', $oElement);
                if ($oElementColor) {
                    // Color
                    $oColor = new Color();
                    $oColor->setRGB($oElementColor->hasAttribute('val') ? $oElementColor->getAttribute('val') : null);
                    // Background
                    $oBackground = new \PhpOffice\PhpPresentation\Slide\Background\Color();
                    $oBackground->setColor($oColor);
                    // Slide Background
                    $oSlide = $this->oPhpPresentation->getActiveSlide();
                    $oSlide->setBackground($oBackground);
                }
                $oElementImage = $xmlReader->getElement('a:blipFill/a:blip', $oElement);
                if ($oElementImage) {
                    $relImg = $this->arrayRels['ppt/slides/_rels/'.$baseFile.'.rels'][$oElementImage->getAttribute('r:embed')];
                    if (is_array($relImg)) {
                        // File
                        $pathImage = 'ppt/slides/'.$relImg['Target'];
                        $pathImage = explode('/', $pathImage);
                        foreach ($pathImage as $key => $partPath) {
                            if ($partPath == '..') {
                                unset($pathImage[$key - 1]);
                                unset($pathImage[$key]);
                            }
                        }
                        $pathImage = implode('/', $pathImage);
                        $contentImg = $this->oZip->getFromName($pathImage);

                        $tmpBkgImg = tempnam(sys_get_temp_dir(), 'PhpPresentationReaderPpt2007Bkg');
                        file_put_contents($tmpBkgImg, $contentImg);
                        // Background
                        $oBackground = new Image();
                        $oBackground->setPath($tmpBkgImg);
                        // Slide Background
                        $oSlide = $this->oPhpPresentation->getActiveSlide();
                        $oSlide->setBackground($oBackground);
                    }
                }
            }

            // Shapes
            foreach ($xmlReader->getElements('/p:sld/p:cSld/p:spTree/*') as $oNode) {
                switch ($oNode->tagName) {
                    case 'p:pic':
                        $this->loadShapeDrawing($xmlReader, $oNode, $oSlide);
                        break;
                    case 'p:sp':
                        $this->loadShapeRichText($xmlReader, $oNode, $oSlide);
                        break;
                    case 'p:graphicFrame':
                        $this->loadGraphicFrame($xmlReader, $oNode, $oSlide);
                        break;
                    default:
                        
                }
            }
            // Layout
            $oSlide = $this->oPhpPresentation->getActiveSlide();
            foreach ($this->arrayRels['ppt/slides/_rels/'.$baseFile.'.rels'] as $valueRel) {
                if ($valueRel['Type'] == 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/slideLayout') {
                    $layoutBasename = basename($valueRel['Target']);
                    if (array_key_exists($layoutBasename, $this->arraySlideLayouts)) {
                        $oSlide->setSlideLayout($this->arraySlideLayouts[$layoutBasename]);
                    }
                    break;
                }
            }
        }
    }

    protected function loadGraphicFrame(XMLReader $document, \DOMElement $node, AbstractSlide $oSlide)
    {
        $oElement = null;

        $childs = $node->getElementsByTagName('graphicData');
        
        if ($childs->length > 0) {
            $oElement = $childs->item(0);
        }

        if (! is_null($oElement) && $oElement->hasChildNodes()) {
            $child = $oElement->firstChild;

            switch ($child->tagName) {
                case 'a:tbl':
                    $this->loadTable($document, $child, $oSlide);
                    break;
                default:
                    
            }
        }
    }

    protected function loadTable(XMLReader $document, \DOMElement $node, AbstractSlide $oSlide)
    {
        $gridColumns = $node->getElementsByTagName('gridCol');
        $gridRows = $node->getElementsByTagName('tr');

        $table = $oSlide->createTableShape($gridColumns->length);

        $slideRow = null;
        $height = null;
        $width = null;
        $rowSpan = null;
        $gridSpan = null;
        $hMerge = null; // if the merge is with merged with the previous of the same row (?)
        $vMerge = null; // if the merge is with merged with the previous of the same column (?)

        $tcs = null;

        $paragraph = null;

        foreach ($gridRows as $row) {
            $slideRow = $table->createRow();

            $height = $row->getAttribute('h');
            $width = $row->getAttribute('w'); // it is on the gridCol element
            
            //these are on the td in each row
            $rowSpan = $row->getAttribute('rowSpan');
            $gridSpan = $row->getAttribute('gridSpan');
            $hMerge = $row->getAttribute('hMerge');
            $vMerge = $row->getAttribute('vMerge');

            if (! empty($height)) {
                $slideRow->setHeight($height);
            }

            if (! empty($width)) {
                $slideRow->setWidth($width);
            }

            $tcs = $row->getElementsByTagName('tc');

            for ($cellIndex=0; $cellIndex < $tcs->length; $cellIndex++) {
                $tc = $tcs[$cellIndex];

                $paragraphs = $tc->getElementsByTagName('p');

                foreach ($paragraphs as $par) {

                    // get all a:p and then from inside each paragraph the a:t
                    $paragraph = $slideRow->getCell($cellIndex)->createParagraph();

                    $textual = '';

                    foreach ($par->getElementsByTagName('t') as $text) {
                        $textual .= $text->textContent;
                    }

                    $paragraph->createText($textual);
                }
            }
        }
    }
}
