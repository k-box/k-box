<?php

use Laracasts\TestDummy\Factory;
use KBox\User;
use KBox\File;
use KBox\Institution;
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

        // build FileAlreadyExistsException

        $ex = new FileAlreadyExistsException($upload_name, $doc);

            $this->assertNotNull($ex->getDescriptor());
            $this->assertEquals(trans('errors.filealreadyexists.generic', [
                'name' => $upload_name,
                'title' => $doc->title
            ]), $ex->getMessage());
        
            $this->assertNull($ex->getFileVersion());

            $ex = new FileAlreadyExistsException($upload_name);

            $this->assertEquals(trans('errors.filealreadyexists.generic', [
                'name' => $upload_name,
                'title' => $upload_name
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

        $doc = factory('KBox\DocumentDescriptor')->create([
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
                'network' => network_name(),
                'title' => $doc->title,
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
                'title' => $doc->title
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
                'user' => $user2->name,
                'email' => $user2->email
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
                'title' => $doc->title,
                'collection' => $collection->name,
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
                'title' => $doc->title,
                'collection' => $collection->name,
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
        $user2 = $this->createAdminUser();
        $doc = $this->createDocument($user2, 'private');

        $ex = new FileAlreadyExistsException('A file name', $doc, $doc->file);

        $this->assertEquals(trans('errors.filealreadyexists.revision_of_document', [
                'title' => $doc->title,
                'user' => $user2->name,
                'email' => $user2->email
            ]), $ex->render($user));
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
                'title' => $doc->title
            ]), $ex->render($user));
    }
}
