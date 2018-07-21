<?php

use Laracasts\TestDummy\Factory;
use KBox\Exceptions\FileAlreadyExistsException;
use KBox\Publication;
use Carbon\Carbon;
use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test the FileAlreadyExistsException for proper message rendering
*/
class FileAlreadyExistsExceptionTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    /**
        * ...
        *
        * @return void
        */
    public function testFileAlreadyExistsConstruction()
    {
        $user = $this->createAdminUser();
        $doc = $this->createDocument($user, 'public');
        $upload_name = 'A file name';

        $ex = new FileAlreadyExistsException($upload_name, $doc);

        $this->assertNotNull($ex->getDescriptor());
        $this->assertEquals(trans('errors.filealreadyexists.generic', [
                'name' => e($upload_name),
                'title' => e($doc->title)
            ]), $ex->getMessage());
        
        $this->assertNull($ex->getFileVersion());

        $ex = new FileAlreadyExistsException($upload_name);

        $this->assertEquals(trans('errors.filealreadyexists.generic', [
                'name' => e($upload_name),
                'title' => e($upload_name)
            ]), $ex->getMessage());
        
        $this->assertNull($ex->getDescriptor());
        $this->assertNull($ex->getFileVersion());
    }

    /**
     * ...
     *
     * @return void
     */
    public function testFileAlreadyExistsForPublicDocument()
    {
        $user = $this->createAdminUser();

        $doc = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => null,
            'file_id' => null,
            'hash' => 'hash',
            'is_public' => true,
            'visibility' => 'public',
        ]);

        Publication::unguard(); // as fields are not mass assignable
        
        $doc->publications()->create([
            'published_at' => Carbon::now()
        ]);

        $ex = new FileAlreadyExistsException('A file name', $doc);

        $this->assertEquals(trans('errors.filealreadyexists.in_the_network', [
                'network' => e(network_name()),
                'title' => e($doc->title),
                'institution' => config('dms.institutionID')
            ]), $ex->render($user));
    }

    /**
     * ...
     *
     * @return void
     */
    public function testFileAlreadyExistsForMyDocument()
    {
        $user = $this->createAdminUser();
        $doc = $this->createDocument($user, 'private');

        $ex = new FileAlreadyExistsException('A file name', $doc);

        $this->assertEquals(trans('errors.filealreadyexists.by_you', [
                'title' => e($doc->title)
            ]), $ex->render($user));
    }

    /**
     * ...
     *
     * @return void
     */
    public function testFileAlreadyExistsForDocumentUploadedByAUser()
    {
        $user = $this->createAdminUser();
        $user2 = $this->createAdminUser();
        $doc = $this->createDocument($user2, 'private');

        $ex = new FileAlreadyExistsException('A file name', $doc);

        $this->assertEquals(trans('errors.filealreadyexists.by_user', [
                'user' => e($user2->name),
                'email' => e($user2->email)
            ]), $ex->render($user));
    }

    /**
     * ...
     *
     * @return void
     */
    public function testFileAlreadyExistsForDocumentInCollectionByYou()
    {
        $user = $this->createAdminUser();

        $collection = $this->createCollection($user);
        
        $doc = $this->createDocument($user, 'private');

        $doc->groups()->save($collection);

        $ex = new FileAlreadyExistsException('A file name', $doc);

        $this->assertEquals(trans('errors.filealreadyexists.incollection_by_you', [
                'title' => e($doc->title),
                'collection' => e($collection->name),
                'collection_link' => route('documents.groups.show', [ 'id' => $collection->id, 'highlight' => $doc->id])
            ]), $ex->render($user));
    }

    /**
     * ...
     *
     * @return void
     */
    public function testFileAlreadyExistsForDocumentInCollectionByUser()
    {
        $user = $this->createAdminUser();
        $user2 = $this->createAdminUser();

        $collection = $this->createCollection($user2, false);

        $doc = $this->createDocument($user2, 'private');

        $doc->groups()->attach($collection);

        $ex = new FileAlreadyExistsException('A file name', $doc);

        $this->assertEquals(trans('errors.filealreadyexists.incollection', [
                'title' => e($doc->title),
                'collection' => e($collection->name),
                'collection_link' => route('documents.groups.show', [ 'id' => $collection->id, 'highlight' => $doc->id])
            ]), $ex->render($user));
    }

    /**
     * ...
     *
     * @return void
     */
    public function testFileAlreadyExistsForDocumentRevisionOfUser()
    {
        $user = $this->createAdminUser();
        $user2 = $this->createAdminUser(['name' => 'Ruthe O\'Keefe']);
        $doc = $this->createDocument($user2, 'private');

        $ex = new FileAlreadyExistsException('A file name', $doc, $doc->file);

        $this->assertEquals(trans('errors.filealreadyexists.revision_of_document', [
                'title' => e($doc->title),
                'user' => e($user2->name),
                'email' => e($user2->email)
            ]), $ex->render($user));

        // -'The document you are uploading is an existing revision of <strong>"Vel reiciendis natus doloremque aut."</strong>, added by Ruthe O'Keefe (jewell84@hotmail.com)'
// +'The document you are uploading is an existing revision of <strong>"Vel reiciendis natus doloremque aut."</strong>, added by Ruthe O'Keefe (jewell84@hotmail.com)'
    }

    /**
     * ...
     *
     * @return void
     */
    public function testFileAlreadyExistsForDocumentRevisionOfMyDocument()
    {
        $user = $this->createAdminUser();
        
        $doc = $this->createDocument($user, 'private');

        $ex = new FileAlreadyExistsException('A file name', $doc, $doc->file);

        $this->assertEquals(trans('errors.filealreadyexists.revision_of_your_document', [
                'title' => e($doc->title)
            ]), $ex->render($user));
    }
}
