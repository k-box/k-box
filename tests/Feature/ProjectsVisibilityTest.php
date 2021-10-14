<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use KBox\Traits\Searchable;

class ProjectsVisibilityTest extends TestCase
{
    use Searchable;
    
    private function createUser($capabilities, $userParams = [])
    {
        return tap(factory(User::class)->create($userParams))->addCapabilities($capabilities);
    }

    public function test_projects_are_not_reported_on_sidebar_if_user_is_a_partner_without_projects()
    {
        config(['dms.hide_projects_if_empty' => true]);

        $user = $this->createUser(Capability::$PARTNER);
        
        $url = route('documents.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertDontSeeText(trans('projects.page_title'));
    }
    
    public function test_projects_are_reported_on_sidebar_if_user_is_a_partner_with_projects()
    {
        config(['dms.hide_projects_if_empty' => true]);

        $project = factory(Project::class)->create();

        $user = $this->createUser(Capability::$PARTNER);

        $project->users()->attach($user);
        
        $url = route('documents.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertSeeText(trans('projects.page_title'));
    }

    public function test_projects_are_reported_on_sidebar_if_user_can_create_projects()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);
           
        $url = route('documents.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertSeeText(trans('projects.page_title'));
    }

    public function test_admin_can_see_projects_section()
    {
        config(['dms.hide_projects_if_empty' => true]);
        
        $user = $this->createUser(Capability::$ADMIN);
           
        $url = route('documents.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertSeeText(trans('projects.page_title'));
    }

    public function test_projects_are_reported_on_sidebar_if_hiding_not_active()
    {
        config(['dms.hide_projects_if_empty' => false]);

        $user = $this->createUser(Capability::$PARTNER);
           
        $url = route('documents.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertSeeText(trans('projects.page_title'));
    }
}
