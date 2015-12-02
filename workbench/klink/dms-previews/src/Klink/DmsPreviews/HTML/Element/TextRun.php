<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @link        https://github.com/PHPOffice/PHPWord
 * @copyright   2010-2014 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace Klink\DmsPreviews\HTML\Element;

/**
 * TextRun element HTML writer
 *
 * @since 0.10.0
 */
class TextRun extends Text
{
    /**
     * Write text run
     *
     * @return string
     */
    public function write()
    {
        $content = '';
//dd($this->element);
//        $content .= '<div class="textrun" style="background:lightgreen">';
//        $content .= '<strong>';

        $has_h1_around=false;
        
//        if(property_exists($this, 'element')){
//            if(method_exists($this->element, 'getParagraphStyle')){
//                if($this->element->getParagraphStyle()){
//                    $content .= '<h1 class="title" style="font-weight:bold">'; //print_r($this->element->getParagraphStyle()->getStyleName(), true);
//                    
//                    $has_h1_around=true;
//                }
//            }
//        }
        
        
        
//        $content .= get_class($element) .' - ';
//        $content .= $this->getParagraphStyle() .' - ';
//        $content .= $this->getNestedLevel() .' - ';
//        $content .= $this->getDocPart() .' - ';
//        $content .= $this->getDocPartId() .' - ';
//        $content .= '</strong>';

        if(empty($this->element->getElements())){
            return '';
        }

        $content .= $this->writeOpening();
        $writer = new Container($this->parentWriter, $this->element);
        $content .= $writer->write();
        $content .= $this->writeClosing();
        
//        if($has_h1_around){
//            $content .= '</h1>';
//        }
//        $content .= '</div>';
//        var_dump($this->element);
//        $content .= '<pre>'.print_r($this->element, true).'</pre>';

        return $content;
    }
}
