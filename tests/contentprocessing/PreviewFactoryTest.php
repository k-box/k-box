<?php

use Tests\BrowserKitTestCase;

use Content\Preview\PreviewFactory;

class PreviewFactoryTest extends BrowserKitTestCase
{
    public function file_class_provider()
    {
        return [
            [__DIR__.'/data/presentation.pptx', 'Content\Preview\PresentationPreview'],
            [__DIR__.'/data/spreadsheet.xlsx', 'Content\Preview\SpreadsheetPreview'],
            [__DIR__.'/data/example.docx', 'Content\Preview\WordDocumentPreview'],
            [__DIR__.'/data/csv1.csv', 'Content\Preview\SpreadsheetPreview'],
            [__DIR__.'/data/text.txt', 'Content\Preview\TextPreview'],
            [__DIR__.'/data/markdown.md', 'Content\Preview\MarkdownPreview'],
            [__DIR__.'/data/googe-drive-doc.gdoc', 'Content\Preview\GoogleDrivePreview'],
            [__DIR__.'/data/googe-drive-presentation.gslides', 'Content\Preview\GoogleDrivePreview'],
            [__DIR__.'/data/googe-drive-spreadsheet.gsheet', 'Content\Preview\GoogleDrivePreview'],
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

        $this->assertInstanceOf('Content\Contracts\Preview', $preview);
        $this->assertInstanceOf($class, $preview);
    }

    /**
     * @dataProvider unsupported_file_class_provider
     * @expectedException Content\Preview\Exception\UnsupportedFileException
     */
    public function testFactoryLoadUnsupported($file)
    {
        $preview = PreviewFactory::load($file);
    }
}
