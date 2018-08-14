<?php

namespace KBox\Documents\Preview;

use KBox\Documents\Contracts\Preview as PreviewContract;
use PhpOffice\PhpSpreadsheet\IOFactory;

use KBox\Documents\FileProperties;

/**
 * Spreadsheet preview
 */
class SpreadsheetPreview implements PreviewContract
{
    private $path = null;

    /**
     * The HTML writer instance
     */
    private $writer = null;
    
    /**
     * The Spreadsheet instance
     */
    private $spreadsheet = null;
    
    public function __construct()
    {
    }

    public function load($path)
    {
        $this->path = $path;

        $this->spreadsheet = IOFactory::load($this->path);

        $this->writer = IOFactory::createWriter($this->spreadsheet, 'Html')->setSheetIndex(null);

        return $this;
    }

    public function css()
    {
        return $this->writer->generateStyles(true);
    }

    public function html()
    {
        return sprintf(
            '<div class="preview__render preview__render--spreadsheet">%1$s</div>',
                $this->writer->generateSheetData()
        );
    }

    /**
     * Build the sheet list for navigation purposes
     */
    public function getNavigation()
    {

        // Fetch sheets
        $sheets = $this->spreadsheet->getAllSheets();

        // Construct HTML
        $html = '<div class="preview__navigation preview__navigation--spreadsheet">';

        // Only if there are more than 1 sheets
        if (count($sheets) > 1) {
            // Loop all sheets
            $sheetId = 0;

            foreach ($sheets as $sheet) {
                $html .= '<a href="#sheet'.$sheetId.'">'.$sheet->getTitle().'</a>'.PHP_EOL;
                ++$sheetId;
            }
        }

        $html .= '</div>';

        return $html;
    }

    public function properties()
    {
        $properties = $this->spreadsheet->getProperties();

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
