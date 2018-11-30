<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Project;
use KBox\Capability;

class ProjectsTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function expected_routes_provider()
    {
        return [
            [ 'projects.index', [] ],
            [ 'projects.show', ['id' => 1] ],
            [ 'projects.create', [] ],
            [ 'projects.store', [] ],
            [ 'projects.edit', ['id' => 1] ],
            [ 'projects.update', ['id' => 1] ],
            [ 'projects.destroy', ['id' => 1] ]
        ];
    }
    
    public function routes_and_capabilities_provider()
    {
        return [
            [ Capability::$ADMIN, ['projects.index', 'projects.show', 'projects.create', 'projects.edit'], 200 ],
            [ Capability::$PROJECT_MANAGER_LIMITED, ['projects.index', 'projects.show', 'projects.create', 'projects.edit'], 200 ],
            [ Capability::$PROJECT_MANAGER, ['projects.index', 'projects.show', 'projects.create', 'projects.edit'], 200 ],
            [ Capability::$DMS_MASTER, ['projects.index', 'projects.show', 'projects.create', 'projects.edit'], 403 ],
            [ Capability::$PARTNER, ['projects.index', 'projects.show', 'projects.create', 'projects.edit'], 403 ],
            [ Capability::$GUEST, ['projects.index', 'projects.show', 'projects.create', 'projects.edit'], 403 ],
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
    
    /**
     * Test the expected project routes are available
     *
     * @dataProvider expected_routes_provider
     * @return void
     */
    public function testProjectRoutesExistence($route_name, $parameters)
    {
        // you will see InvalidArgumentException if the route is not defined
        route($route_name, $parameters);

        $this->assertTrue(true, "Test complete without exceptions");
    }
    
    /**
     * Test if some routes browsed after login are viewable or not and shows the expected page and error code
     *
     * @dataProvider routes_and_capabilities_provider
     * @return void
     */
    public function testProject_UserCanSeePage($caps, $routes, $expected_return_code)
    {
        $params = null;
        $user = null;
        
        foreach ($routes as $route) {
            $user = $this->createUser($caps);
            
            if (strpos($route, 'show') !== -1 || strpos($route, 'edit') !== -1) {
                $project = factory(\KBox\Project::class)->create(['user_id' => $user->id]);
                
                $params = ['projects' => $project->id];
            } else {
                $params = [];
            }
            
            $this->actingAs($user);
            
            $this->visit(route($route, $params));
                
            if ($expected_return_code === 200) {
                $this->assertResponseOk();
                $this->seePageIs(route($route, $params));
                $this->assertViewName($route); // in this case view names are equal to route names
            } else {
                $view = $this->response->original;
                
                $this->assertViewName('errors.'.$expected_return_code);
            }
        }
    }

    /**
     * @dataProvider create_input_provider
     */
    public function testProjectCreate($omit_title = false, $omit_description = false, $omit_user = false)
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER_LIMITED);

        $expected_available_users = $this->createUsers(Capability::$PARTNER, 4);

        \Session::start();
        
        $this->actingAs($user);
        
        $this->visit(route('projects.create'))->seePageIs(route('projects.create'));

        $this->assertResponseOk();

        $this->assertViewHas('manager_id', $user->id);

        $this->assertViewHas('available_users');

        $available_users = $this->response->original->available_users;

        $intersect = array_intersect($expected_available_users->pluck('id')->toArray(), $available_users->pluck('id')->toArray());

        $this->assertEquals($expected_available_users->pluck('id')->toArray(), $intersect);

        // Compile the form (according to the data parameters) and submit it

        if (! $omit_title) {
            $this->type('Project title', 'name');
        }

        if (! $omit_description) {
            $this->type('Project description', 'description');
        }

        if (! $omit_user) {
            $this->select($expected_available_users->first()->id, 'users');
        }

        $this->press(trans('projects.labels.create_submit'));

        // check if the show page is stored

        if ($omit_title) {
            $this->seePageIs(route('projects.create'));

            $this->see(trans('errors.generic_form_error'));

            $this->assertArrayHasKey('errors', $this->response->original->getFactory()->getShared());
            
            $errobag = $this->response->original->getFactory()->getShared()['errors'];

            if ($omit_title) {
                $this->assertTrue($errobag->has('name'));
            }
        } else {
            $this->assertResponseOk();

            $this->assertViewHas('pagetitle', 'Project title');
        }
    }

    public function testProjectEdit()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $user = $project->manager()->first();

        $expected_available_users = $this->createUsers(Capability::$PARTNER, 4);

        \Session::start();
        
        $this->actingAs($user);
        
        $this->visit(route('projects.edit', ['id' => $project->id]))->seePageIs(route('projects.edit', ['id' => $project->id]));

        $this->assertResponseOk();

        $this->assertViewHas('manager_id', $user->id);

        $this->assertViewHas('available_users');

        $available_users = $this->response->original->available_users;

        $intersect = array_intersect($expected_available_users->pluck('id')->toArray(), $available_users->pluck('id')->toArray());

        $this->assertEquals($expected_available_users->pluck('id')->toArray(), $intersect);
    }

    public function testProjectIsAccessibleBy()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER_LIMITED);

        // manages project1
        $project1 = $this->createProject(['user_id' => $user->id]);
        // is added to project2
        $project2 = $this->createProject();
        $project2->users()->attach($user->id);

        $project3 = $this->createProject();

        $this->assertTrue(Project::isAccessibleBy($project1, $user), 'project 1 not accessible');
        $this->assertTrue(Project::isAccessibleBy($project2, $user), 'project 2 not accessible');
        $this->assertFalse(Project::isAccessibleBy($project3, $user), 'project accessible');
    }
    
    public function testProjectDocumentsCount()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_LIMITED);
        
        $service = app('KBox\Documents\Services\DocumentsService');

        $document = $this->createDocument($user);
        $document2 = $this->createDocument($user);
        $document3 = $this->createDocument($user);

        $project1 = $this->createProject(['user_id' => $user->id]);

        $project1_child1 = $this->createProjectCollection($user, $project1);
        $project1_child2 = $this->createProjectCollection($user, $project1_child1);
        
        $service->addDocumentToGroup($user, $document, $project1_child2);
        $service->addDocumentToGroup($user, $document2, $project1_child1);
        $service->addDocumentToGroup($user, $document3, $project1->collection);
        $document = $document->fresh();

        $this->assertEquals(3, $project1->getDocumentsCount());
    }
}
