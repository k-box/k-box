<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\Group;
use KlinkDMS\Capability;
use KlinkDMS\Project;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Collection;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test related to the elastic list filters
*/
class FiltersTest extends TestCase {
    
    use DatabaseTransactions;
    
    
    

    /**
     * Test that the collection column in filters is boxed to sub-collections
     * of the currently opened collection
     */
    public function testFiltersInProjectDisplayOnlySubCollectionsOfTheParent(){

        $project_collection_names = ['Project Root', 'Project First Level', 'Second Level'];

        $user = $this->createUser( Capability::$PROJECT_MANAGER );

        $service = app('Klink\DmsDocuments\DocumentsService');

        $project = null;

        $project_group = null;

        $project_group_root = null;

        $project_childs = count($project_collection_names);

        $documents = collect();
        $collections = collect();

        $descriptor = null;


        foreach ($project_collection_names as $index => $name) {


            $project_group = $service->createGroup($user, $name, null, $project_group, false);

            if($index === 0){
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


        // var_dump($documents->first()->toKlinkDocumentDescriptor());

        $expected_collections = $collections->map(function ($item, $key) {
            return '0:' . $item->id;
        });

        // var_dump($expected_collections);

        $this->actingAs($user);

        $url = route( 'documents.groups.show', ['id' => $collections->first()->id, 's' => '*'] );
        $this->visit( $url )->seePageIs( $url );
        $view = $this->response->original; // is a view
        $composer = app('KlinkDMS\Http\Composers\DocumentsComposer');
        $composer->facets($view);
        $this->response->original = $view;

        $this->assertViewHas('columns');
        $this->assertTrue(isset($view->columns['documentGroups']));

        $collection_filters = collect($view->columns['documentGroups']['items']);
        
        $this->assertEquals($expected_collections, $collection_filters->fetch('term'), 'Filter do not contain the expected project collections');

        $this->assertFalse(in_array(true, $collection_filters->fetch('locked')->all()), 'Locked collections found');
        
        $this->assertFalse(in_array(true, $collection_filters->fetch('selected')->all()), 'Selected collections found, expecting none, because user didn\'t select them');

    }
}