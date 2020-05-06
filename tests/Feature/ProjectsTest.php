<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use KBox\DocumentDescriptor;
use Tests\Concerns\ClearDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use InvalidArgumentException;
use KBox\Events\ProjectCreated;

class ProjectsTest extends TestCase
{
    use DatabaseTransactions, ClearDatabase;
    
    public function expected_routes_provider()
    {
        return [
            [ 'projects.index', [] ],
            [ 'projects.show', ['id' => 1] ],
            [ 'projects.create', [] ],
            [ 'projects.store', [] ],
            [ 'projects.edit', ['id' => 1] ],
            [ 'projects.update', ['id' => 1] ]
        ];
    }
    
    public function routes_and_capabilities_provider()
    {
        return [
            [ Capability::$ADMIN, 'projects.show', 200 ],
            [ Capability::$ADMIN, 'projects.create', 200 ],
            [ Capability::$ADMIN, 'projects.edit', 200 ],
            [ Capability::$PROJECT_MANAGER_LIMITED, 'projects.show', 200 ],
            [ Capability::$PROJECT_MANAGER_LIMITED, 'projects.create', 403 ],
            [ Capability::$PROJECT_MANAGER_LIMITED, 'projects.edit', 403 ],
            [ Capability::$PROJECT_MANAGER, 'projects.show', 200 ],
            [ Capability::$PROJECT_MANAGER, 'projects.create', 200 ],
            [ Capability::$PROJECT_MANAGER, 'projects.edit', 200 ],
            [ [Capability::MANAGE_KBOX], 'projects.show', 403 ],
            [ [Capability::MANAGE_KBOX], 'projects.create', 403 ],
            [ [Capability::MANAGE_KBOX], 'projects.edit', 403 ],
            [ Capability::$PARTNER, 'projects.show', 403 ],
            [ Capability::$PARTNER, 'projects.create', 403 ],
            [ Capability::$PARTNER, 'projects.edit', 403 ],
        ];
    }

    public function create_input_provider()
    {
        return [
            [ false, false, false ],
            [ true, false, false ],
            [ false, true, false ],
            [ false, false, true ],
            [ true, false, true ],
            [ true, true, false ],
            [ false, true, true ],
            [ true, true, true ],
        ];
    }

    private function createUser($capabilities, $userParams = [])
    {
        return tap(factory(User::class)->create($userParams))->addCapabilities($capabilities);
    }
    
    /**
     * Test the expected project routes are available
     *
     * @dataProvider expected_routes_provider
     * @return void
     */
    public function test_routes_are_defined($route_name, $parameters)
    {
        // you will see InvalidArgumentException if the route is not defined
        route($route_name, $parameters);

        $this->assertTrue(true, "Test complete without exceptions");
    }
    

    public function test_destroy_route_is_not_defined()
    {
        $this->expectException(InvalidArgumentException::class);

        route('projects.destroy', 1);
    }


    /**
     * Test if some routes browsed after login are viewable or not and shows the expected page and error code
     *
     * @dataProvider routes_and_capabilities_provider
     * @return void
     */
    public function test_user_can_see_project_route($caps, $route, $expected_return_code)
    {
        $params = [];
        
        $user = $this->createUser($caps);
        
        if (strpos($route, 'show') !== -1 || strpos($route, 'edit') !== -1) {
            $project = factory(Project::class)->create(['user_id' => $user->id]);
            $params = ['projects' => $project->id];
        }
        
        $response = $this->actingAs($user)->get(route($route, $params));
            
        if ($expected_return_code === 200) {
            $response->assertStatus(200);
            $response->assertViewIs($route); // in this case view names are equal to route names
        } else {
            $response->assertViewIs('errors.'.$expected_return_code);
        }
    }

    public function test_projects_index_redirect_to_projects_page()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $response = $this->actingAs($user)->get(route('projects.index'));

        $response->assertRedirect(route('documents.projects.index'));
    }

    public function test_project_create_page()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $expected_available_users = collect([$this->createUser(Capability::$PARTNER)]);

        $response = $this->actingAs($user)->get(route('projects.create'));

        $response->assertStatus(200);

        $response->assertViewIs('projects.create');

        $response->assertViewHas('manager_id', $user->id);

        $response->assertViewHas('available_users');

        $available_users = $response->data('available_users');

        $this->assertEquals($expected_available_users->pluck('id')->toArray(), $available_users->pluck('id')->toArray());
    }

    /**
     * @dataProvider create_input_provider
     */
    public function test_project_store_page($omit_title = false, $omit_description = false, $omit_user = false)
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $expected_available_users = collect([$this->createUser(Capability::$PARTNER)]);

        $params = [
            'manager' => $user->id,
            'name' => $omit_title ? null : 'Project title',
            'description' => $omit_description ? null : 'Project description',
        ];

        if (! $omit_user) {
            $params['users'] = [$expected_available_users->first()->id];
        }

        Event::fake();

        $response = $this->from(route('projects.create'))->actingAs($user)->post(route('projects.store'), $params);

        if ($omit_title) {
            $response->assertRedirect(route('projects.create'));
            $response->assertSessionHasErrors(['name']);
        } else {
            $created_project = Project::where('name', 'Project title')->first();
            $response->assertRedirect(route('documents.groups.show', $created_project->collection_id));

            Event::assertDispatched(ProjectCreated::class, function ($e) use ($created_project, $user) {
                return $e->project->is($created_project) && $e->user->is($user);
            });
        }
    }
    
    public function test_project_cannot_be_created_if_already_existing()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $project = factory(Project::class)->create([
            'user_id' => $user->id
        ]);

        $params = [
            'manager' => $user->id,
            'name' => $project->name,
            'description' => 'Project description',
        ];

        $response = $this->from(route('projects.create'))->actingAs($user)->post(route('projects.store'), $params);

        $response->assertRedirect(route('projects.create'));
        $response->assertSessionHasErrors(['name' => trans('projects.errors.already_existing')]);
    }
    
    public function test_two_users_can_have_equally_named_projects()
    {
        $user1 = $this->createUser(Capability::$PROJECT_MANAGER);
        $user2 = $this->createUser(Capability::$PROJECT_MANAGER);

        $params1 = [
            'manager' => $user1->id,
            'name' => 'A project',
            'description' => 'Project description',
        ];

        $params2 = [
            'manager' => $user2->id,
            'name' => 'A project',
            'description' => 'Project description',
        ];

        $response1 = $this->from(route('projects.create'))->actingAs($user1)->post(route('projects.store'), $params1);

        $response2 = $this->from(route('projects.create'))->actingAs($user2)->post(route('projects.store'), $params2);

        $created_project = Project::where('name', $params1['name'])->count();
        $this->assertEquals(2, $created_project);
    }

    public function test_project_edit()
    {
        $project = factory(Project::class)->create();
        
        $user = $project->manager()->first();

        $expected_available_users = collect([$this->createUser(Capability::$PARTNER)]);

        $response = $this->actingAs($user)->get(route('projects.edit', ['id' => $project->id]));

        $response->assertStatus(200);

        $response->assertViewIs('projects.edit');

        $response->assertViewHas('manager_id', $user->id);

        $response->assertViewHas('available_users');

        $available_users = $response->data('available_users');

        $this->assertEquals($expected_available_users->pluck('id')->toArray(), $available_users->pluck('id')->toArray());
    }
    
    public function test_admin_should_be_able_to_add_itself_as_project_member()
    {
        $project = factory(Project::class)->create();
        
        $user = $project->manager()->first();

        $admin = $this->createUser(Capability::$ADMIN);

        $expected_available_users = collect([$this->createUser(Capability::$PARTNER), $admin])->sortBy('name');

        $response = $this->actingAs($admin)->get(route('projects.edit', ['id' => $project->id]));

        $response->assertStatus(200);

        $response->assertViewIs('projects.edit');

        $response->assertViewHas('manager_id', $user->id);

        $response->assertViewHas('available_users');

        $available_users = $response->data('available_users');

        $this->assertEquals($expected_available_users->pluck('id')->toArray(), $available_users->pluck('id')->toArray());
    }
    
    public function test_other_project_manager_should_not_be_able_to_add_itself_as_project_member()
    {
        $project = factory(Project::class)->create();
        
        $user = $project->manager()->first();

        $prjmanager = $this->createUser(Capability::$PROJECT_MANAGER);

        $response = $this->actingAs($prjmanager)->get(route('projects.edit', ['id' => $project->id]));

        $response->assertStatus(403);
    }

    public function test_project_is_accessible_by_user()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER_LIMITED);

        // manages project1
        $project1 = factory(Project::class)->create(['user_id' => $user->id]);
        // is added to project2
        $project2 = factory(Project::class)->create();
        $project2->users()->attach($user->id);

        $project3 = factory(Project::class)->create();

        $this->assertTrue(Project::isAccessibleBy($project1, $user), 'project 1 not accessible');
        $this->assertTrue(Project::isAccessibleBy($project2, $user), 'project 2 not accessible');
        $this->assertFalse(Project::isAccessibleBy($project3, $user), 'project accessible');
    }
    
    public function test_project_documents_can_be_counted()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_LIMITED);
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $document = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);
        $document2 = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);
        $document3 = factory(DocumentDescriptor::class)->create(['owner_id' => $user->id]);

        $project1 = factory(Project::class)->create(['user_id' => $user->id]);

        $project1_child1 = $service->createGroup($user, 'project_child1', null, $project1->collection, false);
        $project1_child2 = $service->createGroup($user, 'project_child2', null, $project1_child1, false);
        
        $service->addDocumentToGroup($user, $document, $project1_child2);
        $service->addDocumentToGroup($user, $document2, $project1_child1);
        $service->addDocumentToGroup($user, $document3, $project1->collection);
        $document = $document->fresh();

        $this->assertEquals(3, $project1->getDocumentsCount());
    }
}
