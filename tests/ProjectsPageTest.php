<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KlinkDMS\User;
use KlinkDMS\Flags;
use KlinkDMS\Capability;
use KlinkDMS\Traits\Searchable;
use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;

/**
 * Test the Projects page for the Unified Search (routes documents.projects.*)
 */
class ProjectsPageTest extends BrowserKitTestCase
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

    
    
    
     
    
    /**
     * Test the expected project routes are available
     *
     * @dataProvider expected_routes_provider
     * @return void
     */
    public function testProjectPageRoutesExistence($route_name, $parameters)
    {
        
        // you will see InvalidArgumentException if the route is not defined
        
        route($route_name, $parameters);
    }
    
    /**
     * Test if some routes browsed after login are viewable or not and shows the expected page and error code
     *
     * @dataProvider routes_and_capabilities_provider
     * @return void
     */
    public function testProjectPageAccess($caps, $routes, $expected_return_code)
    {
        $this->withKlinkAdapterFake();
        
        $params = null;
        $user = null;
        
        foreach ($routes as $route => $viewname) {
            $user = $this->createUser($caps);
            
            if (strpos($route, 'show') !== false) {
                $project = factory('KlinkDMS\Project')->create(['user_id' => $user->id]);
                
                $params = ['projects' => $project->id];
            } else {
                $params = [];
            }
            
            $this->actingAs($user);
            
            $generated_url = route($route, $params);

            $this->visit($generated_url);
            
            if ($expected_return_code === 200) {
                $this->assertResponseOk();
                $this->seePageIs($generated_url);
                $this->assertViewName($viewname);
            } else {
                $view = $this->response->original;
                
                $this->assertViewName('errors.'.$expected_return_code);
            }
        }
    }

    public function testProjectPageHasSearchBar()
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $this->actingAs($user);

        $generated_url = route('documents.projects.index');

        $this->visit($generated_url);

        $this->see('search-form');
    }

    public function testProjectPageIsListingProjects()
    {
        Flags::enable(Flags::UNIFIED_SEARCH);

        $this->withKlinkAdapterFake();

        // Project listing, but links to collections
        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $document = $this->createDocument($user);

        // manages project1
        $project1 = $this->createProject(['user_id' => $user->id]);
        // is added to project2
        $project2 = $this->createProject();
        $project2->users()->attach($user->id);

        $service = app('Klink\DmsDocuments\DocumentsService');
        $service->addDocumentToGroup($user, $document, $project1->collection);
        $document = $document->fresh();

        $expected_projects = collect([$project1, $project2])->sortBy('name')->map(function ($el) {
            return $el->id;
        });

        $this->actingAs($user);

        $generated_url = route('documents.projects.index');

        $this->visit($generated_url);

        $this->assertViewHas('pagetitle', trans('projects.page_title'));
        $this->assertViewHas('current_visibility', 'private');
        $this->assertViewHas('filter', trans('projects.all_projects'));
        $this->assertViewHas('projects');

        $view = $this->response->original;

        $projects = $view->projects;

        $this->assertNotEmpty($projects, 'empty project list');
        $this->assertCount(2, $projects, 'project count');

        $this->assertEquals(
            array_values($expected_projects->toArray()),
            array_values($projects->pluck('id')->toArray()));
        

        // Test: projectspage shows for each project:
            // - The project manager, in the form of username
            // - The number of members in the project
            // - The total amount of files available in the project
            // - The project creation date will be shown only in details view.

        $this->see($project1->manager->name);
        $this->see($project2->manager->name);
        $this->see($project1->getCreatedAt());
        $this->see($project2->getCreatedAt());
        $this->see(trans_choice('projects.labels.user_count', $project1->users->count(), ['count' => $project1->users->count()]));
        $this->see(trans_choice('projects.labels.user_count', $project2->users->count(), ['count' => $project2->users->count()]));
        $this->see(trans_choice('projects.labels.documents_count', 1, ['count' => 1]));
        $this->see(trans_choice('projects.labels.documents_count', 0, ['count' => 0]));

        // Trick get the facets view to test which columns are available in the elastic list
        $view = $this->response->original; // is a view
        $composer = app('KlinkDMS\Http\Composers\DocumentsComposer');
        $composer->facets($view);
        $this->response->original = $view;

        $this->assertViewHas('columns');

        $columns = $this->response->original->columns;

        $this->assertArraySubset(
            ['properties.language', 'properties.mime_type', 'properties.tag', 'properties.collection'],
            array_keys($columns));
    }

    public function testProjectPageProjectFilterListing()
    {
        $this->markTestSkipped(
            'Needs to be reimplemented.'
          );

        Flags::enable(Flags::UNIFIED_SEARCH);

        $mock = $this->withKlinkAdapterMock();

        $mock->shouldReceive('institutions')->andReturn(factory('KlinkDMS\Institution')->make());
        
        $mock->shouldReceive('isNetworkEnabled')->andReturn(false);

        $mock->shouldReceive('updateDocument')->andReturnUsing(function ($document) {
            return $document->getDescriptor();
        });

        $mock->shouldReceive('facets')->andReturnUsing(function ($facets, $visibility, $term = '*') {
            return FakeKlinkAdapter::generateFacetsResponse($facets, $visibility);
        });

        // test projects are labeled in the elastic list and do not show projects not accessible by the user
        
        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        
        $service = app('Klink\DmsDocuments\DocumentsService');

        $document = $this->createDocument($user);

        // manages project1
        $project1 = $this->createProject(['user_id' => $user->id]);
        // is added to project2
        $project2 = $this->createProject();
        $project2->users()->attach($user->id);

        $service->addDocumentToGroup($user, $document, $project1->collection);
        $document = $document->fresh();

        $personal = $this->createCollection($user, true);
        $service->addDocumentToGroup($user, $document, $personal);
        $document = $document->fresh();

        $mock->shouldReceive('search')->andReturnUsing(function ($terms, $type, $resultsPerPage, $offset, $facets) use ($project1, $personal) {
            // dump(func_get_args());
            $res = FakeKlinkAdapter::generateSearchResponse($terms, $type, $resultsPerPage, $offset, $facets);

            $docFt = array_first(array_filter($res->getFacets(), function ($i) {
                return $i->name === 'documentGroups';
            }));
            
            $prjFt = array_first(array_filter($res->getFacets(), function ($i) {
                return $i->name === 'projectId';
            }));

            $fts = [$project1->collection->toKlinkGroup(), $personal->toKlinkGroup()];
            

            $facetItems = [];
            $facetItem = null;

            foreach ($fts as $term) {
                $facetItem = new KlinkFacetItem();
                $facetItem->term = $term;
                $facetItem->count = 1;
                $facetItems[] = $facetItem;
            }
            $docFt->items = $facetItems;

            $facetItems = [];
            $facetItem = null;

            $prjFts = [$project1->id];

            foreach ($prjFts as $term) {
                $facetItem = new KlinkFacetItem();
                $facetItem->term = $term;
                $facetItem->count = 1;
                $facetItems[] = $facetItem;
            }

            $prjFt->items = $facetItems;
            
            return $res;
        });

        $this->actingAs($user);

        $generated_url = route('documents.projects.index');

        $this->visit($generated_url);

        // Trick get the facets view to test which columns are available in the elastic list
        $view = $this->response->original; // is a view
        $composer = app('KlinkDMS\Http\Composers\DocumentsComposer');
        $composer->facets($view);
        $this->response->original = $view;

        $this->assertViewHas('columns');

        $columns = $this->response->original->columns;
        
        $this->assertArraySubset(
            ['properties.language', 'properties.mime_type', 'properties.tag', 'properties.collection'],
            array_keys($columns));
        dump($columns['properties.tag']);
        $project_filters = collect($columns['properties.tag']['items'])->map(function ($el) {
            return $el->term;
        });
        $collection_filters = collect($columns['properties.collection']['items'])->map(function ($el) {
            return $el->term;
        });

        $this->assertArraySubset([$project1->id], $project_filters->toArray());
        $this->assertArraySubset(['0:'.$project1->collection->id], $collection_filters->toArray());
        $this->assertFalse(in_array($personal->toKlinkGroup(), $collection_filters->toArray()));
    }

    // Test: projectspage clicking on a project redirect to the collection
    // Test: projectspage has project column in filters
    // Test: projectspage has action bar if search is performed
    
    // Test: the project-documents-count cache of a project is cleared when a document is added to the project
    // Test: details page

    // Projects: test avatar upload from project edit/create page
}
