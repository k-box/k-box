<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use KBox\Traits\Searchable;
use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Test the Projects page for the Unified Search (routes documents.projects.*)
 */
class ProjectsPageTest extends TestCase
{
    use Searchable;
    use DatabaseTransactions;
    
    public function expected_routes_provider()
    {
        return [
            [ 'documents.projects.index', [] ],
            [ 'documents.projects.show', ['id' => 1] ],
        ];
    }
    
    public function routes_and_capabilities_provider()
    {
        return [
            [ Capability::$ADMIN, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 200 ],
            [ Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 200 ],
            [ Capability::$PROJECT_MANAGER, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 200 ],
            [ Capability::$DMS_MASTER, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 403 ],
            [ Capability::$PARTNER, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 200 ],
            [ Capability::$GUEST, ['documents.projects.index' => 'documents.projects.projectspage', 'documents.projects.show' => 'documents.projects.detail'], 403 ],
        ];
    }

    private function createUser($capabilities, $userParams = [])
    {
        return tap(factory(\KBox\User::class)->create($userParams))->addCapabilities($capabilities);
    }

    /**
     * Test the expected project routes are available
     *
     * @dataProvider expected_routes_provider
     * @return void
     */
    public function test_route_exists($route_name, $parameters)
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
    public function test_user_can_access_the_projects_page($caps, $routes, $expected_return_code)
    {
        $this->withKlinkAdapterFake();
        
        $params = null;
        $user = null;
        
        foreach ($routes as $route => $viewname) {
            $user = $this->createUser($caps);
            
            if (strpos($route, 'show') !== false) {
                $project = factory(\KBox\Project::class)->create(['user_id' => $user->id]);
                
                $params = ['projects' => $project->id];
            } else {
                $params = [];
            }
            
            $generated_url = route($route, $params);

            $response = $this->actingAs($user)->get($generated_url);
            
            if ($expected_return_code === 200) {
                $response->assertStatus(200);
                $response->assertViewIs($viewname);
            } else {
                
                $response->assertViewIs('errors.'.$expected_return_code);
            }
        }
    }

    public function test_search_is_available()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $generated_url = route('documents.projects.index');

        $response = $this->actingAs($user)->get($generated_url);

        $response->assertSee('search-form');
    }

    public function test_accessible_projects_are_listed()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $managed_project = factory(Project::class)->create(['user_id' => $user->id]);
        $project = factory(Project::class)->create();
        $project->users()->attach($user->id);

        $url = route('documents.projects.index');

        $response = $this->actingAs($user)->get($url);

        $response->assertViewHas('projects');

        $projects = $response->data('projects');

        $this->assertNotEmpty($projects, 'empty project list');
        $this->assertCount(2, $projects, 'project count');

        $this->assertEquals(
            [$managed_project->id, $project->id],
            array_values($projects->pluck('id')->toArray())
        );

        $response->assertViewHas('pagetitle', trans('projects.page_title'));
        $response->assertViewHas('current_visibility', 'private');
        $response->assertViewHas('filter', trans('projects.all_projects'));
        $response->assertSee($managed_project->manager->name);
        $response->assertSee($project->manager->name);
        $response->assertSee($managed_project->getCreatedAt());
        $response->assertSee($project->getCreatedAt());
    }
}
