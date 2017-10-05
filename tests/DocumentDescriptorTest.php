<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Exceptions\FileNamingException;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Capability;

class DocumentDescriptorTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function visibility_provider()
    {
        return [
            [\KlinkVisibilityType::KLINK_PRIVATE],
            [\KlinkVisibilityType::KLINK_PUBLIC],
        ];
    }
    
    public function objects_to_store_as_last_error()
    {
        $ex1 = new FileNamingException('Exception test');
        
        
        $obj = new \stdClass;
        $obj->internal = 'hello';
        
        return [
            [ $ex1, ['message', 'type', 'payload'], 'KlinkDMS\Exceptions\FileNamingException' ],
            [ $obj, ['payload', 'type'], 'stdClass' ],
            [ ['1', '2'], ['payload', 'type'], 'array' ],
            [ ['key' => 'value'], ['payload', 'type'], 'array' ],
            [ 'a string', ['payload', 'type'], 'string' ],
            [ 1, ['payload', 'type'], 'number' ],
            [ -1, ['payload', 'type'], 'number' ],
            [ true, ['payload', 'type'], 'boolean' ],
            [ false, ['payload', 'type'], 'boolean' ],
            [ [[1,2]], ['payload', 'type'], 'array' ],
        ];
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
            $this->assertTrue(property_exists($le, $prop), 'Property '.$prop.' do not exists');
        }
        
        $this->assertEquals($expected_value_for_type, $le->type);
    }
    
    public function testLastErrorSavedDuringIndexing()
    {
        $mock = $this->withKlinkAdapterMock();
        
        $file = factory('KlinkDMS\File')->make();
        $inst = factory('KlinkDMS\Institution')->make();
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $mock->shouldReceive('institutions')->andReturn($inst);
        $mock->shouldReceive('addDocument')->andReturnUsing(function () {
            throw new \KlinkException('Bad Request, hash not equals');
        });
        
        $res = $service->indexDocument($file, 'private', null, null, true);
        
        $this->assertInstanceOf('KlinkDMS\DocumentDescriptor', $res);
        $this->assertEquals(DocumentDescriptor::STATUS_ERROR, $res->status);
        
        $le = $res->last_error;
        
        $this->assertNotNull($le);
        $this->assertEquals('KlinkException', $le->type);
        $this->assertEquals('Bad Request, hash not equals', $le->message);
    }
    
    public function testLastErrorSavedDuringReIndexing()
    {
        $mock = $this->withKlinkAdapterMock();

        $doc = factory('KlinkDMS\DocumentDescriptor')->make();
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $mock->shouldReceive('institutions')->andReturn(factory('KlinkDMS\Institution')->make());
        $mock->shouldReceive('updateDocument')->andReturnUsing(function ($document) {
            throw new \KlinkException('Bad Request, hash not equals');
        });
        
        try {
            $res = $service->reindexDocument($doc, 'private');
            // expecting a "KlinkException: Bad Request" because owner is not specified
        } catch (\KlinkException $ex) {
            $this->assertEquals('Bad Request, hash not equals', $ex->getMessage());
        }
        
        $res = DocumentDescriptor::findOrFail($doc->id);
        
        $this->assertInstanceOf('KlinkDMS\DocumentDescriptor', $res);
        
        $le = $res->last_error;
        
        $this->assertNotNull($le);
        $this->assertEquals('KlinkException', $le->type);
        $this->assertEquals('Bad Request, hash not equals', $le->message);
    }

    /**
     * @dataProvider visibility_provider
     */
    public function testConversionToPrivateKlinkDocumentDescriptor($visibility)
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $document = $this->createDocument($user);
        $document2 = $this->createDocument($user);

        $personal1 = $this->createCollection($user);
        $personal2 = $this->createCollection($user);

        $project1 = $this->createProject();
        $project2 = $this->createProject();

        $project_collection = $this->createProjectCollection($user, $project2);

        // add all the possible details
        // add it to 2 personal collections
        // add it to 2 project collections

        $personal1->documents()->save($document);
        $personal1->documents()->save($document2);
        $personal2->documents()->save($document);
        $project1->collection->documents()->save($document); // first level
        $project_collection->documents()->save($document); // second level

        $document = $document->fresh();
        $document2 = $document2->fresh();

        $descriptor = $document->toKlinkDocumentDescriptor($visibility === \KlinkVisibilityType::KLINK_PUBLIC);
        $descriptor2 = $document2->toKlinkDocumentDescriptor($visibility === \KlinkVisibilityType::KLINK_PUBLIC);

        $this->assertEquals(config('dms.institutionID'), $descriptor->getInstitutionID());
        $this->assertEquals($document->local_document_id, $descriptor->getLocalDocumentID());
        $this->assertEquals($document->hash, $descriptor->getHash());
        $this->assertEquals($document->title, $descriptor->getTitle());
        $this->assertEquals($document->document_uri, $descriptor->getDocumentUri());
        $this->assertEquals($document->thumbnail_uri, $descriptor->getThumbnailURI());
        $this->assertEquals($document->user_uploader, $descriptor->getUserUploader());
        $this->assertEquals($document->user_owner, $descriptor->getUserOwner());
        $this->assertEquals($document->created_at->toRfc3339String(), $descriptor->getCreationDate());
        $this->assertEquals($visibility, $descriptor->getVisibility());
        $this->assertEquals($document->language, $descriptor->getLanguage());
        $this->assertEquals($document->abstract, $descriptor->getAbstract());
        $this->assertEquals($document->mime_type, $descriptor->getMimeType());
        $this->assertTrue(is_array($descriptor->getAuthors()));
        $this->assertTrue(is_array($descriptor->getTitleAliases()));

        if ($visibility === \KlinkVisibilityType::KLINK_PRIVATE) {
            $groups = $descriptor->getDocumentGroups();

            $this->assertTrue(is_array($groups));
            $this->assertNotEmpty($groups, 'collection is empty');
            $this->assertCount(4, $groups, 'collection count');

            $this->assertEquals([
                $personal1->toKlinkGroup(),
                $personal2->toKlinkGroup(),
                $project1->collection->toKlinkGroup(),
                $project_collection->toKlinkGroup(),
            ], $groups);
            
            $projects = $descriptor->getProjects();

            $this->assertTrue(is_array($projects));
            $this->assertNotEmpty($projects, 'projects is empty');
            $this->assertCount(2, $projects, 'projects count');
            $this->assertEquals([
                $project1->id,
                $project2->id,
            ], $projects);

            $this->assertCount(1, $descriptor2->getDocumentGroups(), 'descriptor2 collection count');
            $this->assertEmpty($descriptor2->getProjects());
        } else {
            $this->assertEmpty($descriptor->getDocumentGroups(), 'collection not empty');
            $this->assertEmpty($descriptor->getProjects(), 'projects not empty');
        }
    }

    public function testUUIDCreation()
    {

        // Test UUID creation happens

        $user = $this->createAdminUser();

        $document = $this->createDocument($user);

        $this->assertNotNull($document->uuid);
    }
}
