<?php

namespace Tests\Unit\TextExtraction;

use Tests\TestCase;
use KBox\Documents\ExtractText\ExtractTextFactory;
use KBox\Documents\Contracts\ExtractText;
use KBox\Documents\Preview\Exception\UnsupportedFileException;

class TextExtractionFactoryTest extends TestCase
{
    public function file_class_provider()
    {
        return [
            [__DIR__.'/../../data/csv1.csv', 'KBox\Documents\ExtractText\TextFileExtractor'],
            [__DIR__.'/../../data/text.txt', 'KBox\Documents\ExtractText\TextFileExtractor'],
            [__DIR__.'/../../data/markdown.md', 'KBox\Documents\ExtractText\TextFileExtractor'],
            [__DIR__.'/../../data/keyhole-markup.kml', 'KBox\Documents\ExtractText\KmlExtractor'],
            [__DIR__.'/../../data/a-pdf.pdf', 'KBox\Documents\ExtractText\PdfExtractor'],
            [__DIR__.'/../../data/example.docx', 'KBox\Documents\ExtractText\WordExtractor'],
            [__DIR__.'/../../data/presentation.pptx', 'KBox\Documents\ExtractText\PresentationExtractor'],
        ];
    }
    
    public function unsupported_file_class_provider()
    {
        return [
            [__DIR__.'/../../data/googe-drive-doc.gdoc'],
            [__DIR__.'/../../data/googe-drive-presentation.gslides'],
            [__DIR__.'/../../data/googe-drive-spreadsheet.gsheet'],
            [__DIR__.'/../../data/compressed.zip'],
            [__DIR__.'/../../data/keyhole-markup.kmz'],
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
        $preview = ExtractTextFactory::load($file);

        $this->assertInstanceOf(ExtractText::class, $preview);
        $this->assertInstanceOf($class, $preview);
    }

    /**
     * @dataProvider unsupported_file_class_provider
     */
    public function testFactoryLoadUnsupported($file)
    {
        $this->expectException(UnsupportedFileException::class);

        $preview = ExtractTextFactory::load($file);
    }
}
