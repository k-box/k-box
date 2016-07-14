<?php

use KlinkDMS\Import;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Capability;
use KlinkDMS\Jobs\ImportCommand;
use Klink\DmsMicrosites\Microsite;


class ProjectsTest extends TestCase {
    
    
    use DatabaseTransactions;
    
    public function expected_routes_provider(){
		
		return array( 
			array( 'projects.index', [] ),
			array( 'projects.show', ['id' => 1] ),
			array( 'projects.create', [] ),
			array( 'projects.store', [] ),
			array( 'projects.edit', ['id' => 1] ),
			array( 'projects.update', ['id' => 1] ),
			array( 'projects.destroy', ['id' => 1] )
		);
	}
    
    
    public function routes_and_capabilities_provider(){
		
		return array( 
			array( Capability::$ADMIN, array('projects.index', 'projects.show', 'projects.create', 'projects.edit'), 200 ),
			array( Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH, array('projects.index', 'projects.show', 'projects.create', 'projects.edit'), 200 ),
			array( Capability::$PROJECT_MANAGER, array('projects.index', 'projects.show', 'projects.create', 'projects.edit'), 200 ),
			array( Capability::$DMS_MASTER, array('projects.index', 'projects.show', 'projects.create', 'projects.edit'), 403 ),
			array( Capability::$PARTNER, array('projects.index', 'projects.show', 'projects.create', 'projects.edit'), 403 ),
			array( Capability::$GUEST, array('projects.index', 'projects.show', 'projects.create', 'projects.edit'), 403 ),
		);
	}

    public function create_input_provider(){
		
		return array( 
			array( false, false, false ),
			array( true, false, false ),
			array( false, true, false ),
			array( false, false, true ),
			array( true, false, true ),
			array( true, true, false ),
			array( false, true, true ),
			array( true, true, true ),
		);
	}
    
    
    
     
    
    /**
	 * Test the expected project routes are available
	 *
	 * @dataProvider expected_routes_provider
	 * @return void
	 */
    public function testProjectRoutesExistence($route_name, $parameters){
        
        // you will see InvalidArgumentException if the route is not defined
        
        route( $route_name, $parameters );
        
    }
    
    /**
	 * Test if some routes browsed after login are viewable or not and shows the expected page and error code
	 *
	 * @dataProvider routes_and_capabilities_provider
	 * @return void
	 */
    public function testProject_UserCanSeePage( $caps, $routes, $expected_return_code){
        
        $params = null;
        $user = null;
        
        foreach ($routes as $route) {
            
            $user = $this->createUser($caps);
            
            if( strpos($route, 'show') !== -1 || strpos($route, 'edit') !== -1 ){
                
                $project = factory('KlinkDMS\Project')->create(['user_id' => $user->id]);
                
                $params = ['projects' => $project->id];
            }
            else {
                $params = [];
            }
            
            $this->actingAs($user);
            
            $this->visit( route($route, $params));
                
            if($expected_return_code === 200){
                
                $this->assertResponseOk();
                $this->seePageIs( route($route, $params) );
                $this->assertViewName( $route ); // in this case view names are equal to route names
                
            }
            else {
                $view = $this->response->original;
                
                $this->assertViewName('errors.' . $expected_return_code);
            }
            
        }
        
    }


    /**
     * @dataProvider create_input_provider
     */
    public function testProjectCreate($omit_title = false, $omit_description = false, $omit_user = false){

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $expected_available_users = $this->createUsers(Capability::$PARTNER, 4);

        \Session::start();
        
        $this->actingAs( $user );
        
        $this->visit( route('projects.create') )->seePageIs( route('projects.create') );

        $this->assertResponseOk();

        $this->assertViewHas('manager_id', $user->id);

        $this->assertViewHas('available_users');

        $available_users = $this->response->original->available_users;

        $this->assertEquals($expected_available_users->count(), $available_users->count());


        // Compile the form (according to the data parameters) and submit it

        if(!$omit_title){
            $this->type('Project title', 'name');
        }

        if(!$omit_description){
            $this->type('Project description', 'description');
        }

        if(!$omit_user){
            $this->select($expected_available_users->first()->id, 'users');
        }


        $this->press( trans('projects.labels.create_submit') );

        // check if the show page is stored

        if($omit_title || $omit_user){

            $this->seePageIs( route('projects.create') );

            $this->see(trans('errors.generic_form_error'));

            $this->assertArrayHasKey('errors', $this->response->original->getFactory()->getShared());
            
            $errobag = $this->response->original->getFactory()->getShared()['errors'];

            if($omit_title){
                $this->assertTrue($errobag->has('name'));
            }

            if($omit_user){
                $this->assertTrue($errobag->has('users'));
            }

        }
        else {

            $this->assertResponseOk();

            $this->assertViewHas('project');

        }

    }


    public function testProjectEdit(){

        $project = factory('KlinkDMS\Project')->create();
        
        $user = $project->manager()->first();

        $expected_available_users = $this->createUsers(Capability::$PARTNER, 4);

        \Session::start();
        
        $this->actingAs( $user );
        
        $this->visit( route('projects.edit', ['id' => $project->id]) )->seePageIs( route('projects.edit', ['id' => $project->id]) );

        $this->assertResponseOk();

        $this->assertViewHas('manager_id', $user->id);

        $this->assertViewHas('available_users');

        $available_users = $this->response->original->available_users;

        $this->assertEquals($expected_available_users->count(), $available_users->count());
        
    }
    
    
}
