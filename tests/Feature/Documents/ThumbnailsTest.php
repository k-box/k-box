<?php

namespace Tests\Feature\Documents;

use Tests\TestCase;
use KBox\File;
use KBox\Documents\Facades\Files;
use KBox\Documents\Facades\Thumbnails;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThumbnailsTest extends TestCase
{
    use DatabaseTransactions;

    public function data_document_provider()
    {
        return [
            // true: if the thumbnail path is a default thumbnail
            // false: if the thumbnail path must not be a default thumbnail
            [ 'tests/data/example.docx', true ],
            [ 'tests/data/example.pdf', true ],
            [ 'tests/data/example-presentation.pptx', true ],
            [ 'tests/data/project-avatar.png', false ],
            [ 'tests/data/folder_for_import/folder1/in-folder-1.md', true ],
            [ 'tests/data/users.csv', true ],
        ];
    }

    public function data_url_import_provider()
    {
        return [
            // true: if the thumbnail path is a default thumbnail
            // false: if the thumbnail path must not be a default thumbnail
            [ 'https://klink.asia/', 'text/html; charset=utf-8', true ],
            [ 'https://s3.amazonaws.com/lowres.cartoonstock.com/dating-attachment-jpeg-pdf-files-computer_files-mfln7218_low.jpg', 'image/jpg', false ],
            [ 'https://hectorucsar.files.wordpress.com/2014/08/mafalda-03.pdf', 'application/pdf', true ],
            [ 'http://imgs.xkcd.com/comics/xkcde.png', 'image/png', false ],
        ];
    }

    /**
     * @dataProvider data_document_provider
     */
    public function test_thumbnail_generation_job_generates_thumbnail($path, $expectedDefault)
    {
        // $mock = $this->withKlinkAdapterMock();

        // $service = app('thumbnails');

        // $mock->shouldReceive('generateThumbnailOfWebSite', 'generateThumbnailFromContent')->andReturnUsing(function ($uri, $save_path) use ($expectedDefault) {
        //     if ($expectedDefault) {
        //         throw new Exception('An exception to test default handling');
        //     }

        //     return 'A_simulated_file_content';
        // });
        
        $real_path = base_path($path);
        
        list($mime, $documentType) = Files::recognize($real_path);

        $file = factory(File::class)->create([
            'name' => basename($real_path),
            'hash' => Files::hash($real_path),
            'path' => $real_path,
            'mime_type' => $mime,
            'size' => filesize($real_path),
        ]);
        
        $default_thumb = Thumbnails::fallback($file);

        Thumbnails::queue($file);
        
        $file = File::findOrFail($file->id);
        
        $this->assertNotNull($file->thumbnail_path, 'Thumbnail path is null');
        
        $this->assertNotEmpty($file->thumbnail_path, 'Thumbnail path is empty');

        if ($expectedDefault) {
            $this->assertEquals($default_thumb, $file->thumbnail_path);
        } else {
            $this->assertNotEquals($file->thumbnail_path, $default_thumb);
        }
    }
}
