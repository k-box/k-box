<?php

use Tests\BrowserKitTestCase;

use KBox\Documents\Preview\PreviewFactory;

class PreviewFactoryTest extends BrowserKitTestCase
{
    public function file_class_provider()
    {
        return [
            [__DIR__.'/data/presentation.pptx', 'KBox\Documents\Preview\PresentationPreview'],
            [__DIR__.'/data/spreadsheet.xlsx', 'KBox\Documents\Preview\SpreadsheetPreview'],
            [__DIR__.'/data/example.docx', 'KBox\Documents\Preview\WordDocumentPreview'],
            [__DIR__.'/data/csv1.csv', 'KBox\Documents\Preview\SpreadsheetPreview'],
            [__DIR__.'/data/text.txt', 'KBox\Documents\Preview\TextPreview'],
            [__DIR__.'/data/markdown.md', 'KBox\Documents\Preview\MarkdownPreview'],
            [__DIR__.'/data/googe-drive-doc.gdoc', 'KBox\Documents\Preview\GoogleDrivePreview'],
            [__DIR__.'/data/googe-drive-presentation.gslides', 'KBox\Documents\Preview\GoogleDrivePreview'],
            [__DIR__.'/data/googe-drive-spreadsheet.gsheet', 'KBox\Documents\Preview\GoogleDrivePreview'],
        ];
    }

    public function unsupported_file_class_provider()
    {
        return [
            [__DIR__.'/data/compressed.zip'],
            [__DIR__.'/data/a-pdf.pdf'],
            [__DIR__.'/data/keyhole-markup.kml'],
            [__DIR__.'/data/keyhole-markup.kmz'],
        ];
    }
    
    /**
     * This test verifies that the correct Preview class is returned. The file can also
     * not exists on disk, because it is not readed at this stage
     *
     * @dataProvider file_class_provider
     */
    public function testFactoryLoad($file, $class)
    {
        $preview = PreviewFactory::load($file);

        $this->assertInstanceOf('KBox\Documents\Contracts\Preview', $preview);
        $this->assertInstanceOf($class, $preview);
    }

    /**
     * @dataProvider unsupported_file_class_provider
     * @expectedException KBox\Documents\Preview\Exception\UnsupportedFileException
     */
    public function testFactoryLoadUnsupported($file)
    {
        $preview = PreviewFactory::load($file);
    }
}
