<?php

use KlinkDMS\User;
use KlinkDMS\Capability;
use KlinkDMS\Project;
use Illuminate\Support\Collection;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Klink\DmsAdapter\KlinkFacetItem;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;

/*
 * Test related to the elastic list filters
*/
class FiltersTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    
    

    /**
     * Test that the collection column in filters is boxed to sub-collections
     * of the currently opened collection
     */
    public function testFiltersInProjectDisplayOnlySubCollectionsOfTheParent()
    {
        $this->markTestSkipped(
            'Needs to be reimplemented.'
          );
          
        $mock = $this->withKlinkAdapterMock();

        $project_collection_names = ['Project Root', 'Project First Level', 'Second Level'];

        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $service = app('Klink\DmsDocuments\DocumentsService');

        $mock->shouldReceive('addDocument', 'updateDocument')->andReturnUsing(function ($doc) {
            $descr = $doc->getDescriptor();

            return $descr;
        });

        $project = null;

        $project_group = null;

        $project_group_root = null;

        $project_childs = count($project_collection_names);

        $documents = collect();
        $collections = collect();

        $descriptor = null;

        foreach ($project_collection_names as $index => $name) {
            $project_group = $service->createGroup($user, $name, null, $project_group, false);

            if ($index === 0) {
                $project = Project::create([
                    'name' => $name,
                    'user_id' => $user->id,
                    'collection_id' => $project_group->id,
                ]);

                $project_group_root = $project_group;
            }
            
            $descriptor = $this->createDocument($user);
            $service->addDocumentToGroup($user, $descriptor, $project_group);

            $documents->push($descriptor->fresh());
            $collections->push($project_group->fresh());
        }

        $prj_grp = $service->createGroup($user, 'Another different project', null, null, false);

        $project = Project::create([
            'name' => $prj_grp->name,
            'user_id' => $user->id,
            'collection_id' => $prj_grp->id,
        ]);
        
        
        $service->addDocumentToGroup($user, $documents->first(), $prj_grp);

        $documents = $documents->each(function ($item, $key) {
            $item = $item->fresh();
        });

        $expected_collections = $collections->map(function ($item, $key) {
            return '0:'.$item->id;
        });

        $mock->shouldReceive('search')->andReturnUsing(function ($terms, $type = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $offset = 0, $facets = null) use ($expected_collections) {
            $partial = FakeKlinkAdapter::generateSearchResponse($terms, $type, $resultsPerPage, $offset, $facets);

            $ft = array_filter($partial->getFacets(), function ($a) {
                return $a->name === 'documentGroups';
            });

            $ft = $ft[0];

            $newItems = [];
            $ftItem = null;

            foreach ($expected_collections as $col) {
                $ftItem = new KlinkFacetItem;
                $ftItem->term = $col;
                $ftItem->count = 1;
                $newItems[] = $ftItem;
            }

            $ft->items = $newItems;

            return $partial;
        });

        $this->actingAs($user);

        $url = route('documents.groups.show', ['id' => $collections->first()->id, 's' => '*']);
        $this->visit($url)->seePageIs($url);
        $view = $this->response->original; // is a view
        $composer = app('KlinkDMS\Http\Composers\DocumentsComposer');
        $composer->facets($view);
        $this->response->original = $view;

        $this->assertViewHas('columns');
        $this->assertTrue(isset($view->columns['documentGroups']));

        $collection_filters = collect($view->columns['documentGroups']['items']);
        
        $this->assertEquals($expected_collections, $collection_filters->pluck('term'), 'Filter do not contain the expected project collections');

        $this->assertFalse(in_array(true, $collection_filters->pluck('locked')->all()), 'Locked collections found');
        
        $this->assertFalse(in_array(true, $collection_filters->pluck('selected')->all()), 'Selected collections found, expecting none, because user didn\'t select them');
    }
}
