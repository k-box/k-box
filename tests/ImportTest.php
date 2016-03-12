<?php

use KlinkDMS\Import;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Capability;
use KlinkDMS\Jobs\ImportCommand;


class ImportTest extends TestCase {
    
    
    use DatabaseTransactions, /*DatabaseMigrations,*/ WithoutMiddleware;
    
    public function user_provider(){
		
		return array( 
			array(Capability::$ADMIN, 200),
			array(Capability::$DMS_MASTER, 403),
			array(Capability::$PROJECT_MANAGER, 200),
			array(Capability::$PARTNER, 403),
			array(Capability::$GUEST, 403),
		);
	}
    
    
    public function url_provider(){
		
		return array( 
			array( 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_RUS.pdf' ),
            array( 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_KYR-small.pdf' ),
            array( 'http://www.conservation.org/Pages/default.aspx' ),
            array( 'http://www.conservation.org/publications/Documents/CI_Ecosystem-based-Adaptation-South-Africa-Vulnerability-Assessment-Brochure.pdf' ),
            array( 'https://www.armeniatree.org/en/' ),
            array( 'https://www.armeniatree.org/en/initiatives.asp' ),
            array( 'https://www.change.org/p/unfccc-united-nations-framework-convention-on-climate-change-ensure-that-the-impact-of-climate-change-on-mountain-peoples-and-ecosystems-is-fully-addressed-in-the-unfccc-cop21-new-climate-deal?source_location=petitions_share_skip' ),
            array( 'https://www.change.org/p/unfccc-united-nations-framework-convention-on-climate-change-ensure-that-the-impact-of-climate-change-on-mountain-peoples-and-ecosystems-is-fully-addressed-in-the-unfccc-cop21-new-climate-deal?source_location' ),
            array( 'http://klink.asia' ),
		);
	}
    
    
    public function url_import_provider(){
		
		return array( 
			array( 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_RUS.pdf', 'application/pdf' ),
            array( 'http://www.kg.undp.org/content/dam/kyrgyzstan/Publications/env-energy/KGZ_Insulating_Your_House_280114_KYR-small.pdf', 'application/pdf' ),
            array( 'http://www.conservation.org/Pages/default.aspx', 'text/html' ),
            array( 'http://www.conservation.org/publications/Documents/CI_Ecosystem-based-Adaptation-South-Africa-Vulnerability-Assessment-Brochure.pdf', 'application/pdf' ),
            array( 'https://www.armeniatree.org/en/', 'text/html' ),
            array( 'https://www.armeniatree.org/en/initiatives.asp', 'text/html' ),
            array( 'https://www.change.org/p/unfccc-united-nations-framework-convention-on-climate-change-ensure-that-the-impact-of-climate-change-on-mountain-peoples-and-ecosystems-is-fully-addressed-in-the-unfccc-cop21-new-climate-deal?source_location=petitions_share_skip', 'text/html' ),
            array( 'https://www.change.org/p/unfccc-united-nations-framework-convention-on-climate-change-ensure-that-the-impact-of-climate-change-on-mountain-peoples-and-ecosystems-is-fully-addressed-in-the-unfccc-cop21-new-climate-deal?source_location', 'text/html' ),
            array( 'http://klink.asia', 'text/html' ),
		);
	} 
    
    /**
	 * Test the import page loading based on user capabilities
	 *
	 * @dataProvider user_provider
	 * @return void
	 */
    public function testImportPageLoading($caps, $expected_code){
        
        $this->beginDatabaseTransaction();
        
		$user = $this->createUser($caps);
		
		$this->actingAs($user);
		
		$this->visit( route('import') );
             
		if($expected_code === 200){
			$this->assertResponseOk();
            $this->see('Import');
            $this->seePageIs( route('import') );
		}
		else {
			$view = $this->response->original;
			
			$this->assertEquals('errors.' . $expected_code, $view->name());
		}
        
    }
    
    
    /**
     * Test if the Import job is raised when creating an import
     *
     * @dataProvider url_provider
     */
    public function testImportControllerCreatesImportJob( $url ){
        
        // $this->expectsJobs(ImportCommand::class);
        
        $user = $this->createAdminUser();
        
        $this->actingAs($user);
        
        $this->json( 'POST', route('import') , [
            'from' => 'remote',
            'remote_import' => $url
        ])->seeJson();
        
        // var_dump( $this->response->getContent() );
        
        $this->assertResponseOk();
        
        $stored_import = Import::fromUser($user->id)->first();
        $stored_import_count = Import::fromUser($user->id)->count();
        
        $this->assertEquals(1, $stored_import_count);
        $this->assertNotNull(1, $stored_import);
        $this->assertTrue(!!$stored_import->is_remote, "Stored import not marked as remote");
        $this->assertEquals($url, $stored_import->file->original_uri);
        
    }
    
    /**
     * Test if the Import job is raised when creating an import
     *
     * @dataProvider url_provider
     */
    public function testImportControllerCreateWithFileAlreadyExistsException( $url ){
        
        $user = $this->createAdminUser();
        
        $this->actingAs($user);
        
        $this->json( 'POST', route('import') , [
            'from' => 'remote',
            'remote_import' => $url
        ])->seeJson();
        
        $this->assertResponseOk();

        $this->json( 'POST', route('import') , [
            'from' => 'remote',
            'remote_import' => $url
        ])->seeJson([
            'remote_import' => trans('errors.import.url_already_exists', ['url' => $url])
        ]);
        
        $this->assertResponseStatus(422);
        
    }
    
    
    
    /**
     * @dataProvider url_import_provider
     */
    public function testImportFromUrlJob($url, $mime_type){
        
        // create an ImportJob and run it
        
        $save_path = Config::get('dms.upload_folder') . DIRECTORY_SEPARATOR . md5($url);
        
        if(file_exists($save_path)){
            unlink($save_path);
        }
        
        
        $user = $this->createAdminUser();
        
        $file = factory('KlinkDMS\File')->create([
            'mime_type' => '',
            'size' => 0,
            'path' => $save_path,
            'user_id' => $user->id,
            'original_uri' => $url
        ]);
        
        $import = factory('KlinkDMS\Import')->create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'is_remote' => true
        ]);

        dispatch(new ImportCommand($user, $import)); //make sure to have QUEUE_DRIVER=sync in testing.env
        
        
        $saved_import = Import::with('file')->findOrFail($import->id);
        
        
        $this->assertEquals(KlinkDMS\Import::STATUS_COMPLETED, $saved_import->status);
        
        $this->assertEquals(KlinkDMS\Import::MESSAGE_COMPLETED, $saved_import->status_message);
        
        $this->assertTrue(file_exists( $saved_import->file->path ), "File do not exists");
        
        $this->assertEquals($saved_import->bytes_expected, $saved_import->bytes_received, "Bytes expected and received are not equals");
        
        $this->assertNotEquals(md5($url), $saved_import->file->hash, "File hash not changed");
        
        $this->assertNotEquals(0, $saved_import->file->size, "File Size not changed");
        $this->assertEquals($saved_import->bytes_received, $saved_import->file->size, "File Size not equal to downloaded size");
        
        $this->assertContains($mime_type, $saved_import->file->mime_type, "Inferred mime type is different than what is expected");
        
        // var_dump($saved_import->toArray());
        
    }
    
}
