<?php

use KlinkDMS\Import;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Project;
use KlinkDMS\Jobs\ThumbnailGenerationJob;
use GuzzleHttp\Client;

use Illuminate\Foundation\Application;
use KlinkDMS\Console\Commands\ThumbnailGenerationCommand;

class ThumbnailsTest extends TestCase {
    
    use DatabaseTransactions;
    
    
    public function test_document_provider(){
		
		return array( 
            // true: if the thumbnail path is a default thumbnail
            // false: if the thumbnail path must not be a default thumbnail
			array( 'tests/data/example.docx', true ),
            array( 'tests/data/example.pdf', false ),
            array( 'tests/data/example-presentation.pptx', false ),
            array( 'tests/data/project-avatar.png', false ),
            array( 'tests/data/folder_for_import/folder1/in-folder-1.md', true ),
            array( 'tests/data/users.csv', true ),
		);
	}

    public function test_url_import_provider(){
		
		return array( 
            // true: if the thumbnail path is a default thumbnail
            // false: if the thumbnail path must not be a default thumbnail
            array( 'https://klink.asia/', 'text/html; charset=utf-8', false ),
            array( 'https://s3.amazonaws.com/lowres.cartoonstock.com/dating-attachment-jpeg-pdf-files-computer_files-mfln7218_low.jpg', 'image/jpg', false ),
            array( 'https://hectorucsar.files.wordpress.com/2014/08/mafalda-03.pdf', 'application/pdf', false ),
            array( 'http://imgs.xkcd.com/comics/xkcde.png', 'image/png', false ),
		);
	}
    
    public function mime_type_provider(){
        
        
        return array(

            array('post', 'images/web-page.png'),
            array('page', 'images/web-page.png'),
            array('node', 'images/web-page.png'),
            array('text/html', 'images/web-page.png'),
            array('application/msword', 'images/document.png'),
            array('application/vnd.ms-excel', 'images/spreadsheet.png'),
            array('application/vnd.ms-powerpoint', 'images/presentation.png'),
            array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'images/spreadsheet.png'),
            array('application/vnd.openxmlformats-officedocument.presentationml.presentation', 'images/presentation.png'),
            array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'images/document.png'),
            array('application/pdf', 'images/document.png'),
            array('text/uri-list', 'images/web-page.png'),
            array('image/jpg', 'images/image.png'),
            array('image/jpeg', 'images/image.png'),
            array('image/gif', 'images/image.png'),
            array('image/png', 'images/image.png'),
            array('image/tiff', 'images/image.png'),
            array('text/plain', 'images/text-document.png'),
            array('application/rtf', 'images/text-document.png'),
            array('text/x-markdown', 'images/text-document.png'),
            array('application/vnd.google-apps.document', 'images/document.png'),
            array('application/vnd.google-apps.drawing', 'images/image.png'),
            array('application/vnd.google-apps.form', 'images/form.png'),
            array('application/vnd.google-apps.fusiontable', 'images/spreadsheet.png'),
            array('application/vnd.google-apps.presentation', 'images/presentation.png'),
            array('application/vnd.google-apps.spreadsheet', 'images/spreadsheet.png'),
            array('application/vnd.google-earth.kml+xml', 'images/geodata.png'),
            array('application/vnd.google-earth.kmz', 'images/geodata.png'),
            array('application/rar', 'images/archive.png'),
            array('application/zip', 'images/archive.png'),
            array('application/x-mimearchive', 'images/web-page.png'),
            array('video/x-ms-vob', 'images/dvd.png'),
            array('content/DVD', 'images/dvd.png'),
            array('video/x-ms-wmv', 'images/video.png'),
            array('video/x-ms-wmx', 'images/video.png'),
            array('video/x-ms-wm', 'images/video.png'),
            array('video/avi', 'images/video.png'),
            array('video/divx', 'images/video.png'),
            array('video/x-flv', 'images/video.png'),
            array('video/quicktime', 'images/video.png'),
            array('video/mpeg', 'images/video.png'),
            array('video/mp4', 'images/video.png'),
            array('video/ogg', 'images/video.png'),
            array('video/webm', 'images/video.png'),
            array('video/x-matroska', 'images/video.png'),
            array('video/3gpp', 'images/video.png'),
            array('video/3gpp2', 'images/video.png'),
            array('text/csv', 'images/spreadsheet.png'),
            array('message/rfc822', 'images/email.png'),
            array('application/vnd.ms-outlook', 'images/email.png'),
            array('application/octet-stream', 'images/document.png'),

		);
        
	}
    
    
    /**
     * @dataProvider test_document_provider
     */
    public function testThumbnailGenerationJob( $path, $expectedDefault ){

        $mock = $this->withKlinkAdapterMock();

        $service = app('thumbnails');

        $mock->shouldReceive('generateThumbnailOfWebSite', 'generateThumbnailFromContent')->andReturnUsing( function($uri, $save_path) use($expectedDefault)
        {
            if($expectedDefault)
            {
                throw new Exception('An exception to test default handling');
            }

            return 'A_simulated_file_content';
        });
        
        
        $real_path = base_path( $path );
        
        $file = factory('KlinkDMS\File')->create([
            'name' => basename($real_path),
            'hash' => \KlinkDocumentUtils::generateDocumentHash($real_path),
            'path' => $real_path,
            'mime_type' => \KlinkDocumentUtils::get_mime($real_path), 
            'size' => filesize($real_path),
        ]);
        
        $default_thumb = $this->invokePrivateMethod($service, 'getDefaultThumbnail', [$file->mime_type]);

        dispatch(new ThumbnailGenerationJob($file)); //make sure to have QUEUE_DRIVER=sync in testing.env
        
        $file = KlinkDMS\File::findOrFail($file->id);
        
        $this->assertNotNull($file->thumbnail_path);
        
        $this->assertNotEmpty($file->thumbnail_path);

        if($expectedDefault)
        {
            $this->assertEquals($default_thumb, $file->thumbnail_path);
        }
        else
        {
            $this->assertNotEquals($file->thumbnail_path, $default_thumb);
        }
        
    }
    
    /**
     * Simulates an import from URL and build the thumbnail for that imports
     * @dataProvider test_url_import_provider
     */
    public function testThumbnailGenerationFromUrlImport( $url, $mimeType, $expectedDefault ){

        $mock = $this->withKlinkAdapterMock();

        $service = app('thumbnails');
        $documentsService = app('Klink\DmsDocuments\DocumentsService');

        $mock->shouldReceive('generateThumbnailOfWebSite', 'generateThumbnailFromContent')->andReturnUsing( function($uri, $save_path, $variant_save_path = null) use($expectedDefault)
        {
            if($expectedDefault)
            {
                throw new Exception('An exception to test default handling');
            }

            return file_put_contents(isset($variant_save_path) && !is_null($variant_save_path) ? $variant_save_path : $save_path, 'A_simulated_file_content');
        });
        
        $filename = $documentsService->extractFileNameFromUrl($url);
        $real_path = $documentsService->constructLocalPathForImport($filename, md5($url));

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $url,
            // You can set any number of default request options.
            'timeout'  => 60.0,
        ]);
        
        if(@!is_file($real_path))
        {
            $response = $client->request('GET', $url, ['sink' => $real_path]);
            
            $response_headers = $response->getHeaders();
            
            $good = $response->getStatusCode() === 200;
            
            $this->assertTrue($good, 'HTTP Download failed');
        }
        
        $file = factory('KlinkDMS\File')->create([
            'name' => basename($real_path),
            'hash' => \KlinkDocumentUtils::generateDocumentHash($real_path),
            'path' => $real_path,
            'original_uri' => $url,
            'mime_type' => $mimeType, 
            'size' => filesize($real_path),
        ]);
        
        $default_thumb = $this->invokePrivateMethod($service, 'getDefaultThumbnail', [$file->mime_type]);

        dispatch(new ThumbnailGenerationJob($file)); //make sure to have QUEUE_DRIVER=sync in testing.env
        
        $file = KlinkDMS\File::findOrFail($file->id);
        
        $this->assertNotNull($file->thumbnail_path);
        
        $this->assertNotEmpty($file->thumbnail_path);

        if($expectedDefault)
        {
            $this->assertEquals($default_thumb, $file->thumbnail_path);
        }
        else
        {
            $this->assertNotEquals($file->thumbnail_path, $default_thumb);
        }
        
    }
    
    /**
     * @dataProvider mime_type_provider
     */
    public function testDefaultThumbnailsForMimeType($mimeType, $expected_thumb){
        
        $service = app('thumbnails');
        
        $path = $this->invokePrivateMethod($service, 'getDefaultThumbnail', [$mimeType]);
        
        $full_expected_path = public_path($expected_thumb);
        
        $this->assertEquals($full_expected_path, $path);
        
        $this->assertTrue(@is_file($path));
        
        $this->assertTrue(@is_file($full_expected_path));
        
    }
    
    /**
     * @dataProvider test_document_provider
     */
    public function testThumbnailGenerationConsole( $path, $unused ){

        $command = new ThumbnailGenerationCommand();
        
        $real_path = base_path( $path );
        
        $file = factory('KlinkDMS\File')->create([
            'name' => basename($real_path),
            'hash' => \KlinkDocumentUtils::generateDocumentHash($real_path),
            'path' => $real_path,
            'mime_type' => \KlinkDocumentUtils::get_mime($real_path), 
            'size' => filesize($real_path),
        ]);
        
        
        $document = factory('KlinkDMS\DocumentDescriptor')->create([
            'owner_id' => $file->user_id,
            'file_id' => $file->id,
        ]);
        
        $res = $this->runArtisanCommand($command, [
            'documents' => [ $document->id ]
        ]);
        
        $this->assertRegExp('/Generating thumbnails/', $res);
        $this->assertRegExp('/1 document/', $res);
        $this->assertRegExp('/100/', $res);
        
    }
    
    
    /**
     * @expectedException Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testThumbnailGenerationConsoleWithNonExistingDocuments(){

        $command = new ThumbnailGenerationCommand();
        
        $res = $this->runArtisanCommand($command, [
            'documents' => ['89999999']
        ]);
        
    }
    
    
    public function testThumbnailGenerationFromFileUpload(){

        $this->withKlinkAdapterFake();
        
        $real_path = base_path('tests/data/example.pdf');
        
        copy($real_path, str_replace('example.pdf', 'example2.pdf', $real_path));
        
        $real_path = base_path('tests/data/example2.pdf');
        
        $uploadFile = new Symfony\Component\HttpFoundation\File\UploadedFile( 
            $real_path, 
            'example.pdf',
            'application/pdf',
            filesize($real_path),
            null,
            true //Test
            );
        
        $this->expectsJobs(ThumbnailGenerationJob::class);
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $descriptor = $service->importFile($uploadFile, $this->createAdminUser());
        
        $this->assertNotNull($descriptor);
        
    }
    
    
    
    
    protected function runCommand($command, $input = [], $output = null)
    {
        if(is_null($output)){
             $output = new Symfony\Component\Console\Output\NullOutput;
        }
        
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input), $output);
    }
}
