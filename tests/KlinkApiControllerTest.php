<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\Capability;
use Illuminate\Support\Facades\Artisan;


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Basic tests of show route on the KlinkApiController
*/
class KlinkApiControllerTest extends TestCase {
    
	use DatabaseTransactions;

	/**
	 * ...
	 *
	 * @return void
	 */
	public function testDocumentShowForDocumentInProject( )
	{

		$this->withKlinkAdapterFake();

		$user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
		$user_accessing_the_document = $this->createUser( Capability::$PARTNER );
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $document = $this->createDocument($user);

        $project1 = $this->createProject(['user_id' => $user->id]);
		$project1->users()->attach($user_accessing_the_document->id);

        $project1_child1 = $this->createProjectCollection($user, $project1);
        $service->addDocumentToGroup($user, $document, $project1_child1);
        
        $url = route( 'klink_api', ['id' => $document->local_document_id, 'action' => 'document'] );

		$this->actingAs($user_accessing_the_document);
        
        $this->visit( $url );
  		
        $this->assertResponseOk();

		$this->assertViewName('documents.preview');
		
	}

	public function testDocumentShowForSharedDocument( )
	{

		$user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
		$user_accessing_the_document = $this->createUser( Capability::$PARTNER );

        $document = $this->createDocument($user);

		$document->shares()->create(array(
			'user_id' => $user->id,
			'sharedwith_id' => $user_accessing_the_document->id, //the id 
			'sharedwith_type' => get_class($user_accessing_the_document), //the class
			'token' => hash( 'sha512', '$token_content' ),
		));

        
        $url = route( 'klink_api', ['id' => $document->local_document_id, 'action' => 'document'] );

		$this->actingAs($user_accessing_the_document);
        
        $this->visit( $url );

		$this->assertViewName('documents.preview');
		
	}

	public function testDocumentShowForPublicDocument( )
	{

		$user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
		$user_accessing_the_document = $this->createUser( Capability::$PARTNER );

        $document = $this->createDocument($user, 'public');

        
        $url = route( 'klink_api', ['id' => $document->local_document_id, 'action' => 'document'] );

		$this->actingAs($user_accessing_the_document);
        
        $this->visit( $url );

		$this->assertViewName('documents.preview');
		
	}

	public function testDocumentShowForPublicDocumentWithNoLogin( )
	{

		$this->withKlinkAdapterFake();

		$user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
		$user_accessing_the_document = $this->createUser( Capability::$PARTNER );

		$service = app('Klink\DmsDocuments\DocumentsService');

        $document = $this->createDocument($user, 'public');

		$project1 = $this->createProject(['user_id' => $user->id]);
		$project1->users()->attach($user_accessing_the_document->id);

        $project1_child1 = $this->createProjectCollection($user, $project1);
        $service->addDocumentToGroup($user, $document, $project1_child1);


        
        $url = route( 'klink_api', ['id' => $document->local_document_id, 'action' => 'document'] );
        
        $this->visit( $url );

		$this->assertViewName('documents.preview');
		
	}
	
	public function testDocumentShowForDocumentNotSharedNorInProject( )
	{

		$user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
		$user_accessing_the_document = $this->createUser( Capability::$PARTNER );

        $document = $this->createDocument($user);

        
        $url = route( 'klink_api', ['id' => $document->local_document_id, 'action' => 'document'] );

		$this->actingAs($user_accessing_the_document);
        
        $this->visit( $url );

		$this->assertViewName('errors.403');
		
	}

	public function testDocumentShowForPrivateDocumentNotInCollectionOrShared( )
	{

		$user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
		$user_accessing_the_document = $this->createUser( Capability::$PARTNER );

        $document = $this->createDocument($user, 'private');

        
        $url = route( 'klink_api', ['id' => $document->local_document_id, 'action' => 'document'] );

		$this->actingAs($user);
        
        $this->visit( $url );

		$this->assertViewName('documents.preview');
		
	}

	public function testDocumentShowForPrivateDocumentNotInCollectionOrSharedViaOtherUser( )
	{

		$user = $this->createUser( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
		$user_accessing_the_document = $this->createUser( Capability::$PARTNER );

        $document = $this->createDocument($user, 'private');

        
        $url = route( 'klink_api', ['id' => $document->local_document_id, 'action' => 'document'] );

		$this->actingAs($user_accessing_the_document);
        
        $this->visit( $url );

		$this->assertViewName('errors.403');
		
	}
    
	
}