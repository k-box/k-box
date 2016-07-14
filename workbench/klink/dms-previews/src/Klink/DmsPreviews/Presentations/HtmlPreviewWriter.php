<?php

namespace Klink\DmsPreviews\Presentations;

// use PhpOffice\PhpWord\PhpWord;
// use PhpOffice\PhpWord\Writer\AbstractWriter;
// use PhpOffice\PhpWord\Writer\WriterInterface;

/**
 * HTML writer
 *
 * Not supported: PreserveText, PageBreak, Object
 * @since 0.10.0
 */
class HtmlPreviewWriter /*extends AbstractWriter implements WriterInterface*/
{
    // /**
    //  * Is the current writer creating PDF?
    //  *
    //  * @var boolean
    //  */
    // protected $isPdf = false;

    // /**
    //  * Footnotes and endnotes collection
    //  *
    //  * @var array
    //  */
    // protected $notes = array();

    // /**
    //  * Create new instance
    //  */
    // public function __construct(PhpWord $phpWord = null)
    // {
    //     $this->setPhpWord($phpWord);

    //     $this->parts = array('Head', 'Body');
    //     foreach ($this->parts as $partName) {
    //         //$partClass = 'PhpOffice\\PhpWord\\Writer\\HTML\\Part\\' . $partName;
    //         $partClass = 'Klink\\DmsPreviews\\HTML\\Part\\' . $partName;
            
    //         if (class_exists($partClass)) {
    //             /** @var \PhpOffice\PhpWord\Writer\HTML\Part\AbstractPart $part Type hint */
    //             $part = new $partClass();
    //             $part->setParentWriter($this);
    //             $this->writerParts[strtolower($partName)] = $part;
    //         }
    //     }
    // }

    // /**
    //  * Save PhpWord to file.
    //  *
    //  * @param string $filename
    //  * @return void
    //  * @throws \PhpOffice\PhpWord\Exception\Exception
    //  */
    // public function save($filename = null)
    // {
    //     $this->writeFile($this->openFile($filename), $this->getContent());
    // }

    // /**
    //  * Get content
    //  *
    //  * @return string
    //  * @since 0.11.0
    //  */
    // public function getContent()
    // {
    //     $content = $this->getWriterPart('Body')->write();
        
    //     $content = str_replace('<body>', '', $content);
    //     $content = str_replace('</body>', '', $content);
        
    //     return $content;
    // }

    // /**
    //  * Get is PDF
    //  *
    //  * @return bool
    //  */
    // public function isPdf()
    // {
    //     return $this->isPdf;
    // }

    // /**
    //  * Get notes
    //  *
    //  * @return array
    //  */
    // public function getNotes()
    // {
    //     return $this->notes;
    // }

    // /**
    //  * Add note.
    //  *
    //  * @param int $noteId
    //  * @param string $noteMark
    //  * @return void
    //  */
    // public function addNote($noteId, $noteMark)
    // {
    //     $this->notes[$noteId] = $noteMark;
    // }

    // /**
    //  * Write document
    //  *
    //  * @return string
    //  * @deprecated 0.11.0
    //  * @codeCoverageIgnore
    //  */
    // public function writeDocument()
    // {
    //     return $this->getContent();
    // }
}