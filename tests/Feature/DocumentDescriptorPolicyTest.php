<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Group;
use KBox\Shared;
use KBox\Project;
use Tests\TestCase;
use KBox\DocumentDescriptor;
use KBox\Policies\DocumentDescriptorPolicy;
use Illuminate\Foundation\Testing\WithFaker;

class DocumentDescriptorPolicyTest extends TestCase
{
    use  WithFaker;

    public function test_deny_see_username()
    {
        $user = User::factory()->partner()->create();

        $descr = DocumentDescriptor::factory()->create();

        $can = (new DocumentDescriptorPolicy())->see_owner($user, $descr);

        $this->assertFalse($can);
    }
    
    public function test_deny_if_user_is_trashed()
    {
        $user = User::factory()->partner()->create();

        $descr = DocumentDescriptor::factory()->create();

        $descr->owner->delete();

        $can = (new DocumentDescriptorPolicy())->see_owner($user, $descr);

        $this->assertFalse($can);
    }

    public function test_allow_see_username()
    {
        $descr = DocumentDescriptor::factory()->create();

        $can = (new DocumentDescriptorPolicy())->see_owner($descr->owner, $descr);

        $this->assertTrue($can);
    }

    public function test_allow_see_username_for_admin()
    {
        $user = User::factory()->admin()->create();

        $descr = DocumentDescriptor::factory()->create();

        $can = (new DocumentDescriptorPolicy())->see_owner($user, $descr);

        $this->assertTrue($can);
    }
    
    public function test_project_member_is_allowed_to_see_username_if_uploader_is_member_of_project()
    {
        // create project
        $manager = User::factory()->projectManager()->create();
        $project = Project::factory()->create(['user_id' => $manager->id]);

        // add member
        $member_one = User::factory()->partner()->create();
        $project->users()->attach($member_one->id);

        // add second member
        $member_two = User::factory()->partner()->create();
        $project->users()->attach($member_two->id);

        // upload doc using second member
        $descr = DocumentDescriptor::factory()->create(['owner_id' => $member_two->id]);

        //add doc to collection
        $project->collection->documents()->save($descr);

        // check if first member can see
        $this->assertTrue((new DocumentDescriptorPolicy())->see_owner($member_one, $descr, $project));
        $this->assertTrue((new DocumentDescriptorPolicy())->see_owner($member_one, $descr, $project->collection));

        // check if project manager can see
        $this->assertTrue((new DocumentDescriptorPolicy())->see_owner($manager, $descr, $project));
        $this->assertTrue((new DocumentDescriptorPolicy())->see_owner($manager, $descr, $project->collection));
    }
    
    public function test_member_of_other_project_is_not_allowed_to_see_username_if_document_uploaded_in_another_project()
    {
        // create project
        $manager = User::factory()->projectManager()->create();
        $project = Project::factory()->create(['user_id' => $manager->id]);
        
        $manager_two = User::factory()->projectManager()->create();
        $project_two = Project::factory()->create(['user_id' => $manager_two->id]);

        // add member
        $member_one = User::factory()->partner()->create();
        $project->users()->attach($member_one->id);

        // add members to second project
        $member_two = User::factory()->partner()->create();
        $project_two->users()->attach($member_two->id);
        $project->users()->attach($member_two->id);
        
        $member_three = User::factory()->partner()->create();
        $project_two->users()->attach($member_three->id);

        // upload doc
        $descr = DocumentDescriptor::factory()->create(['owner_id' => $member_three->id]);

        //add doc to collection
        $project_two->collection->documents()->save($descr);

        $this->assertFalse((new DocumentDescriptorPolicy())->see_owner($member_one, $descr));

        $this->assertFalse((new DocumentDescriptorPolicy())->see_owner($manager, $descr));

        $this->assertTrue((new DocumentDescriptorPolicy())->see_owner($member_two, $descr));

        $this->assertTrue((new DocumentDescriptorPolicy())->see_owner($manager_two, $descr));
    }
    
    public function test_direct_document_share_give_permission_to_see_owner()
    {
        $user = User::factory()->partner()->create();
        
        $descr = DocumentDescriptor::factory()->create();

        Shared::factory()->create([
            'user_id' => $descr->owner->id,
            'shareable_id' => $descr->id,
            'sharedwith_id' => $user->id,
        ]);

        $this->assertTrue((new DocumentDescriptorPolicy())->see_owner($user, $descr));
    }
    
    public function test_collection_share_give_permission_to_see_owner()
    {
        $user = User::factory()->partner()->create();

        $owner = User::factory()->partner()->create();

        $collection = Group::factory()->create([
            'user_id' => $owner->id,
        ]);
        
        $descr = DocumentDescriptor::factory()->create([
            'owner_id' => $owner->id
        ]);

        $collection->documents()->attach($descr->id);

        $share = Shared::factory()->create([
            'user_id' => $descr->owner->id,
            'shareable_id' => $collection->id,
            'shareable_type' => get_class($collection),
            'sharedwith_id' => $user->id,
        ]);

        $this->assertTrue((new DocumentDescriptorPolicy())->see_owner($user, $descr));
    }
}
