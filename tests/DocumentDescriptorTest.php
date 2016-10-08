<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Exceptions\FileNamingException;
use KlinkDMS\Exceptions\FileAlreadyExistsException;
use KlinkDMS\DocumentDescriptor;

class DocumentDescriptorTest extends TestCase
{
    
    use DatabaseTransactions;
    
    
    
    public function objects_to_store_as_last_error(){

        $ex1 = new FileNamingException('Exception test');
        
        
        $obj = new \stdClass;
        $obj->internal = 'hello';
        
		return array( 
			array( $ex1, ['message', 'type', 'payload'], 'KlinkDMS\Exceptions\FileNamingException' ),
			array( $obj, ['payload', 'type'], 'stdClass' ),
			array( ['1', '2'], ['payload', 'type'], 'array' ),
			array( ['key' => 'value'], ['payload', 'type'], 'array' ),
			array( 'a string', ['payload', 'type'], 'string' ),
			array( 1, ['payload', 'type'], 'number' ),
			array( -1, ['payload', 'type'], 'number' ),
			array( true, ['payload', 'type'], 'boolean' ),
			array( false, ['payload', 'type'], 'boolean' ),
			array( [[1,2]], ['payload', 'type'], 'array' ),
		);
	}
    
    
    /**
     * Test if the last_error field stores data and the retrieval is comfortable
     *
     * @dataProvider objects_to_store_as_last_error
     * @return void
     */
    public function testLastErrorStoreAndRetrieve($obj, $expected_property_in_deserialized_object, $expected_value_for_type)
    {
        
        $descr = factory('KlinkDMS\DocumentDescriptor')->make();
        $descr->last_error = $obj;
        $saved = $descr->save();
        
        $this->assertTrue($saved);
        
        $retrieved = DocumentDescriptor::findOrFail($descr->id);
        
        $this->assertNotNull($retrieved);
        
        $le = $retrieved->last_error;
        
        $this->assertNotNull($le);
        
        foreach ($expected_property_in_deserialized_object as $prop) {
            $this->assertTrue(property_exists($le, $prop), 'Property ' . $prop . ' do not exists');
        }
        
        $this->assertEquals($expected_value_for_type, $le->type);
        
    }
    
    public function testLastErrorSavedDuringIndexing(){
        
        
        $file = factory('KlinkDMS\File')->make();
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $res = $service->indexDocument($file, 'private', null, null, true);
        // expecting a "KlinkException: Bad Request" because owner is not specified
        
        
        $this->assertInstanceOf('KlinkDMS\DocumentDescriptor', $res);
        
        $le = $res->last_error;
        
        $this->assertNotNull($le);
        $this->assertEquals('KlinkException', $le->type);
        $this->assertEquals('Bad Request', $le->message);

    }
    
    public function testLastErrorSavedDuringReIndexing(){
        
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->make();
        
        $service = app('Klink\DmsDocuments\DocumentsService');
        
        $doc->owner_id = null;
        $doc->save();
        
        try{
            $res = $service->reindexDocument($doc, 'private');
            // expecting a "KlinkException: Bad Request" because owner is not specified
        }catch(\Exception $ex){
            $this->assertInstanceOf('InvalidArgumentException', $ex);
        }
        
        $res = DocumentDescriptor::findOrFail($doc->id);
        
        $this->assertInstanceOf('KlinkDMS\DocumentDescriptor', $res);
        
        $le = $res->last_error;
        
        $this->assertNotNull($le);
        $this->assertEquals('InvalidArgumentException', $le->type);
        $this->assertEquals('The User Uploader must be a non empty or null string', $le->message);

    }
}
