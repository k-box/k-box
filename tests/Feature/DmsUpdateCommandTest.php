<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Option;
use KlinkDMS\Institution;
use KlinkDMS\Console\Commands\DmsUpdateCommand;

class DmsUpdateCommandTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function verify_that_uuid_are_added_to_existing_document_descriptors()
    {
        $this->withKlinkAdapterMock();

        $docs = factory('KlinkDMS\DocumentDescriptor', 3)->create(['uuid' => "00000000-0000-0000-0000-000000000000"]);

        $doc_ids = $docs->pluck('id')->toArray();
        
        // making sure that the install script thinks an update must be performed
        Option::create(['key' => 'c', 'value' => ''.time()]);

        $count_with_null_uuid = DocumentDescriptor::local()->withNullUuid()->count();

        $this->assertEquals($docs->count(), $count_with_null_uuid, 'Query cannot retrieve descriptors with null UUID');

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'generateDocumentsUuid');

        $this->assertEquals(3, $updated, 'Not all documents have been updated');
        
        $ret = DocumentDescriptor::local()->whereIn('id', $doc_ids)->get(['id', 'uuid']);

        $this->assertEquals(3, $ret->count(), 'Not found the same documents originally created');

        //second invokation of the same command

        $updated = $this->invokePrivateMethod($command, 'generateDocumentsUuid');

        $this->assertEquals(0, $updated, 'Some UUID has been regenerated');
    }

    public function test_default_institution_is_created()
    {
        $this->withKlinkAdapterFake();
        
        $current = Institution::current();

        if (! is_null($current)) {
            $current->delete();
        }

        $command = new DmsUpdateCommand();

        $updated = $this->invokePrivateMethod($command, 'createDefaultInstitution');

        $this->assertNotNull(Institution::current());
    }

    public function test_user_affiliation_is_moved_to_organization()
    {
        $institution = factory('KlinkDMS\Institution')->create();
        $users = factory('KlinkDMS\User', 3)->create([
            'institution_id' => $institution->id
        ]);

        $user_ids = $users->pluck('id')->toArray();

        $command = new DmsUpdateCommand();
        
        $updated = $this->invokePrivateMethod($command, 'updateUserOrganizationAttributes');

        $this->assertEquals($users->count(), $updated);
    }
}
