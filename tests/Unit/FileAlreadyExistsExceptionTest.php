<?php

namespace Tests\Unit;

use KBox\User;
use KBox\File;
use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use KBox\Publication;
use KBox\DocumentDescriptor;
use Illuminate\Foundation\Testing\WithFaker;
use KBox\Exceptions\FileAlreadyExistsException;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test the FileAlreadyExistsException for proper message rendering
*/
class FileAlreadyExistsExceptionTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    public function testFileAlreadyExistsConstruction()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

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

    public function testFileAlreadyExistsForPublicDocument()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $doc = factory(DocumentDescriptor::class)->create([
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
                'title' => e($doc->title)
            ]), $ex->render($user));
    }

    public function testFileAlreadyExistsForMyDocument()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $doc = $this->createDocument($user, 'private');

        $ex = new FileAlreadyExistsException('A file name', $doc);

        $this->assertEquals(trans('errors.filealreadyexists.by_you', [
                'title' => e($doc->title)
            ]), $ex->render($user));
    }

    public function testFileAlreadyExistsForDocumentUploadedByAUser()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $user2 = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $doc = $this->createDocument($user2, 'private');

        $ex = new FileAlreadyExistsException('A file name', $doc);

        $this->assertEquals(trans('errors.filealreadyexists.by_user', [
                'user' => e($user2->name),
                'email' => e($user2->email)
            ]), $ex->render($user));
    }

    public function testFileAlreadyExistsForDocumentInCollectionByYou()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $collection = $this->createCollection($user);
        
        $doc = $this->createDocument($user, 'private');

        $doc->groups()->save($collection);

        $ex = new FileAlreadyExistsException('A file name', $doc);

        $this->assertEquals(trans('errors.filealreadyexists.incollection_by_you', [
                'title' => e($doc->title),
                'collection' => e($collection->name),
                'collection_link' => route('documents.groups.show', [ 'group' => $collection->id, 'highlight' => $doc->id])
            ]), $ex->render($user));
    }

    public function testFileAlreadyExistsForDocumentInCollectionByUser()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $user2 = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $collection = $this->createCollection($user2, false);

        $doc = $this->createDocument($user2, 'private');

        $doc->groups()->attach($collection);

        $ex = new FileAlreadyExistsException('A file name', $doc);

        $this->assertEquals(trans('errors.filealreadyexists.incollection', [
                'title' => e($doc->title),
                'collection' => e($collection->name),
                'collection_link' => route('documents.groups.show', [ 'group' => $collection->id, 'highlight' => $doc->id])
            ]), $ex->render($user));
    }

    public function testFileAlreadyExistsForDocumentRevisionOfUser()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $user2 = tap(factory(User::class)->create(['name' => 'Ruthe O\'Keefe']), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        $doc = $this->createDocument($user2, 'private');

        $ex = new FileAlreadyExistsException('A file name', $doc, $doc->file);

        $this->assertEquals(trans('errors.filealreadyexists.revision_of_document', [
                'title' => e($doc->title),
                'user' => e($user2->name),
                'email' => e($user2->email)
            ]), $ex->render($user));
    }

    public function testFileAlreadyExistsForDocumentRevisionOfMyDocument()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $doc = $this->createDocument($user, 'private');

        $ex = new FileAlreadyExistsException('A file name', $doc, $doc->file);

        $this->assertEquals(trans('errors.filealreadyexists.revision_of_your_document', [
                'title' => e($doc->title)
            ]), $ex->render($user));
    }

    private function createDocument(User $user, $visibility = 'private')
    {
        $template = base_path('tests/data/example.txt');
        $destination = storage_path('documents/example-document.txt');

        copy($template, $destination);

        $file = factory(File::class)->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $destination,
            'hash' => hash_file('sha512', $destination)
        ]);
        
        $doc = factory(DocumentDescriptor::class)->create([
            'institution_id' => $user->institution_id,
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'language' => 'en',
            'is_public' => $visibility === 'private' ? false : true,
            'copyright_usage' => 'C',
            'copyright_owner' => collect(['name' => 'owner name', 'website' => 'https://something.com'])
        ]);

        return $doc;
    }

    private function createCollection(User $user, $is_personal = true)
    {
        $service = app('KBox\Documents\Services\DocumentsService');

        return $service->createGroup($user, $this->faker()->name.$user->id, null, null, $is_personal);
    }
}
