<?php

// use KlinkDMS\Import;
// use Illuminate\Foundation\Testing\TestCase;
// use Illuminate\Support\Facades\Log;
// use KlinkDMS\User;
// use KlinkDMS\File;
// use Session;


class ImportTest extends TestCase {

	public function testSomething()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

 //        private $files = array();

 //        /**
	//  * Creates the application.
	//  *
	//  * @return \Illuminate\Foundation\Application
	//  */
	// public function createApplication()
	// {
	// 	$app = require __DIR__.'/../bootstrap/app.php';

	// 	$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

	// 	return $app;
	// }
 //        public function __construct($name = null, array $data = array(), $dataName = '') {
 //            parent::__construct($name, $data, $dataName);
 //        }
 //        public function setUp() {
 //            parent::setUp();
 //            Session::start();
 //        }
 //        private function beTheUser(){
 //            $this->be(User::all()->first());
 //        }
 //        private function allFiles($parent){
 //            $children = Import::myChildren($parent)->get();
 //            if(count($children)>0){
 //                foreach($children as $c){
 //                    $file = File::find($c->file_id);
 //                    if($file->is_folder){
 //                        $this->allFiles($c->id);
 //                    }else{
 //                        array_push($this->files,$file);
 //                    }
 //                }
 //            }
 //        }
        
 //        //non posso accedere alla pag documents/import/id via ajax
 //        public function testAccessImportPageAjaxLoggedIn(){
 //            $this->beTheUser();
 //            $import = Import::all()->first();
 //            if($import!=null){
 //                $response = $this->call('POST','documents/import/'.$import->id,array(
 //                    '_token' => Session::token()
 //                ));
 //                $this->assertResponseStatus('200');
 //            }
            
 //            //test reale solo se ho degli import nel database
 //            $this->assertTrue(TRUE);
 //        }
        
 //        //numero file da status = numero file db
 //        public function testVerifyImportStatus(){
 //            $this->beTheUser();

 //            $import = Import::all()->first();
 //            if($import != null){
 //                $this->allFiles($import->id);
 //                $request= $this->call('POST','documents/import/'.$import->id, array(
 //                    '_token' => Session::token()
 //                ))->getContent();
 //                $json = json_decode($request,true);
 //                $this->assertTrue(count($this->files)===count($json['completed'])+count($json['not_completed']));
 //            }
 //            $this->assertTrue(true);
 //        }
 //        //verifico task completati se hanno bytes_expected==bytes_completed
 //        public function testBytesExpeceted(){
 //            $all = Import::allCompleted();
            
 //            foreach($all as $i){
 //                if($i->bytes_expected!==$i->bytes_completed){
 //                    $this->assertTrue(false);
 //                }
 //            }
 //            $this->assertTrue(true);
 //        }
        
        
 //        //verificare l'estensione del file che coincide con il mimetype
 //        public function testVerifyFileExtensions(){
 //            $imports = Import::allRoots()->get();
 //            foreach($imports as $i){
 //                $this->allFiles($i->id);
 //            }
 //            foreach($this->files as $f){
 //                //find the ext of a given shared  file or remote url
 //                $remote = explode('.',$f->original_uri)[count(explode('.',$f->original_uri)-1)];
 //                $local  = explode('.',$f->path)[count(explode('.',$f->path)-1)];
 //                //check the one with the local
                
 //                //assert false if doesn't match
 //                if($local!==$remote){
 //                    $this->assertTrue(false);
 //                }
 //            }
 //            $this->assertTrue(true);
 //        }
        

}
