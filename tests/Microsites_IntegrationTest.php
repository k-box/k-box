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


class Microsites_IntegrationTest extends TestCase {
    
    
    use DatabaseTransactions;
    
    public function expected_routes_provider(){
		
		return array( 
			array( 'microsites.slug', ['slug' => 'test'] ),
			array( 'microsites.index', [] ),
			array( 'microsites.show', ['id' => 1] ),
			array( 'microsites.create', [] ),
			array( 'microsites.store', [] ),
			array( 'microsites.edit', ['id' => 1] ),
			array( 'microsites.update', ['id' => 1] ),
			array( 'microsites.destroy', ['id' => 1] )
		);
	}
    
    public function microsite_routes_and_expected_page_provider_before_login(){
		
		return array( 
			array( 'microsites.slug', ['slug' => 'test'], 'microsites.slug' ),
			array( 'microsites.index', [], 'auth.login' ),
			array( 'microsites.show', ['id' => 1], 'microsites.show' ),
			array( 'microsites.create', [], 'auth.login' ),
			array( 'microsites.store', [], 'auth.login' ),
			array( 'microsites.edit', ['id' => 1], 'auth.login' ),
			array( 'microsites.update', ['id' => 1], 'auth.login' ),
			array( 'microsites.destroy', ['id' => 1], 'auth.login' )
		);
	}
    
    public function expected_auth_for_routes_provider(){
		
		return array( 
			array(Capability::$ADMIN, 200),
			array(Capability::$PROJECT_MANAGER, 200),
            array(Capability::$DMS_MASTER, 403),
			array(Capability::$PARTNER, 403),
			array(Capability::$GUEST, 403),
		);
	}
    
    
    public function invalid_microsite_creation_request(){
		// this parameters are the ones that will be manipulated to create an invalid request
		return array( 
            // request, attribute to be modified, kind of error expected
			array(array('project' => null), 'project', 'authorize'),
			array(array('project' => ''), 'project', 'authorize'),
			array(array('project' => 'string'), 'project', 'authorize'),
			array(array('project' => 125), 'project', 'authorize'),
			array(array('title' => null), 'title', 'validation'),
			array(array('title' => ''), 'title', 'validation'),
			array(array('title' => 125), 'title', 'validation'),
			array(array('slug' => 125), 'slug', 'validation'),
			array(array('slug' => '125'), 'slug', 'validation'),
			array(array('slug' => null), 'slug', 'validation'),
			array(array('slug' => ''), 'slug', 'validation'),
			array(array('logo' => ''), 'logo', 'validation'),
			array(array('logo' => 'helpme'), 'logo', 'validation'),
			array(array('logo' => 'http://helpme.com/'), 'logo', 'validation'),
			array(array('logo' => null), 'logo', 'validation'),
			array(array('logo' => array()), 'logo', 'validation'),
			array(array('default_language' => array()), 'default_language', 'validation'),
			array(array('default_language' => ''), 'default_language', 'validation'),
			array(array('default_language' => null), 'default_language', 'validation'),
			array(array('default_language' => 5), 'default_language', 'validation'),
			array(array('default_language' => 'lang'), 'default_language', 'validation'),
			array(array('default_language' => '5'), 'default_language', 'validation'),
			array(array('default_language' => 'l'), 'default_language', 'validation'),
			array(array('default_language' => '51'), 'default_language', 'validation'),
			array(array('default_language' => 'l1'), 'default_language', 'validation'),
			array(array('content' => 'l1'), 'content', 'validation'),
			array(array('content' => null), 'content', 'validation'),
			array(array('content' => ''), 'content', 'validation'),
            array(array('menu' => 'l1'), 'menu', 'validation'),
			array(array('menu' => null), 'menu', 'validation'),
			array(array('menu' => ''), 'menu', 'validation'),
            array(array('hero_image' => ''), 'hero_image', 'validation'),
			array(array('hero_image' => 'helpme'), 'hero_image', 'validation'),
			array(array('hero_image' => 'http://helpme.com'), 'hero_image', 'validation'),
			array(array('hero_image' => null), 'hero_image', 'validation'),
			array(array('hero_image' => array()), 'hero_image', 'validation'),
		);
	}
    
    
     
    
    /**
	 * Test the expected microsites routes are available
	 *
	 * @dataProvider expected_routes_provider
	 * @return void
	 */
    public function testMicrositeRoutesExistence($route_name, $parameters){
        
        // you will see InvalidArgumentException if the route is not defined
        
        route( $route_name, $parameters );
        
    }
    
    /**
     * @dataProvider microsite_routes_and_expected_page_provider_before_login
     */
    public function testMicrositeRouteAuthentication_NoLogin( $route, $parameters, $expected_route ){

		$this->visit( route( $route, $parameters ) )->seePageIs( route( $expected_route, $expected_route === 'auth.login' ? [] : $parameters ) );
        
    }
    
    /**
     * Test if the Project show page of a specific project has the microsite section on the UI
     */
    public function testMicrositeCreateActionsOnProjectShowPage(){
        
        $project = factory('KlinkDMS\Project')->create();
        
        $this->actingAs($project->manager()->first());
        
        $this->visit( route('projects.show', ['id' => $project->id]))
            ->see('Microsite')
            ->see( trans('microsites.actions.create') );
            
        $this->click('microsite_create');
        
        $this->seePageIs( route( 'microsites.create', ['project' => $project->id] ) );
        
        $this->see( trans('microsites.pages.create', ['project' => $project->name]) );
        
        $this->see( trans('microsites.actions.publish') );
        
        $this->assertViewHas('pagetitle');
        $this->assertViewHas('project');
        
    }
    
    
    public function testMicrositeManageActionsVisibilityOnProjectShowPage(){
        
        $project = factory('KlinkDMS\Project')->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
            'institution_id' => $project_manager->institution_id,
        ]);
        
        $this->actingAs( $project_manager );
        
        $this->visit( route('projects.show', ['id' => $project->id]))
            ->see('Microsite')
            ->see( trans('microsites.actions.delete') )
            ->see( trans('microsites.actions.view_site') )
            ->see( trans('microsites.actions.edit') );
        
    }
    
    
    
    /**
	 * Test if the routes are only available to the correct users 
     * based on authentication
	 *
	 * @dataProvider expected_auth_for_routes_provider
	 * @return void
	 */
    public function testMicrositeRouteAuthentication( $caps, $expected_code ){
        
        $institution_count = \KlinkDMS\Institution::count();
        
        if($institution_count == 0){
            $institution = factory(KlinkDMS\Institution::class)->create()->id;
        }
        else {
            $institution = \KlinkDMS\Institution::all()->random()->id;
        }
        
        $user = $this->createUser($caps, [
            'institution_id' => $institution->id
        ]);
        
        $project = factory('KlinkDMS\Project')->create([
            'user_id' => $user->id
        ]);
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project->user_id,
            'institution_id' => $project->manager()->instituion_id,
        ]);
        
        $this->markTestIncomplete( 'microsites route authentication test needs to be implemented' );
        
        // without login
        // with login
        // with login using a different user than the creator
        
        // $user = $this->createUser($caps);
		
		// $this->actingAs($user);
		// 
		// $this->visit( route('import') );
        //      
		// if($expected_code === 200){
		// 	$this->assertResponseOk();
        //     $this->see('Import');
        //     $this->seePageIs( route('import') );
		// }
		// else {
		// 	$view = $this->response->original;
		// 	
		// 	$this->assertEquals('errors.' . $expected_code, $view->name());
		// }
        
    }
    
    public function testMicrositeCreateInvokedWithoutProjectParameter(){
        
        $project = factory('KlinkDMS\Project')->create();
        
        $project_manager = $project->manager()->first();
        
        \Session::start();
        
        $this->actingAs( $project_manager );
        
        $this->visit( route('projects.show', ['id' => $project->id ]) );
        
        $this->visit( route('microsites.create') );
        
        $this->seePageIs( route('projects.show', ['id' => $project->id ]) );
        
        $this->see( trans('microsites.errors.create_no_project') );
        
    }
    
    
    public function testMicrositeCreateOnProjectWithExistingMicrosite(){
        
        $project = factory('KlinkDMS\Project')->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
            'institution_id' => $project_manager->institution_id,
        ]);
        
        $this->actingAs( $project_manager );
        
        $this->visit( route('projects.show', ['id' => $project->id ]) );
        
        $this->visit( route('microsites.create', ['project' => $project->id]));
        
        $this->seePageIs( route('projects.show', ['id' => $project->id ]) );
        
        $this->see( trans('microsites.errors.create_already_exists', ['project' => $project->name]) );
    }
    
    
    public function testMicrositeStoreWithValidData(){
        
        $project = factory('KlinkDMS\Project')->create();
        
        $project_manager = $project->manager()->first();
        
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id,
            'institution_id' => $project_manager->institution_id,
        ])->toArray();
        
        unset($microsite_request['project_id']);
        unset($microsite_request['institution_id']);
        unset($microsite_request['user_id']);
        
        $microsite_request['content'] = [
            'en' => [
                'title' => 'Example page',
                'slug' => 'Example page',
                'content' => 'Example page content',
            ]
        ];
        
        \Session::start();
        
        $this->actingAs( $project_manager );
        
        $microsite_request['_token'] = csrf_token();
        
        $this->post( route('microsites.store'), $microsite_request);
        
        $microsite = Microsite::where('slug', $microsite_request['slug'])->first();
        
        $this->assertNotNull($microsite, 'Cannot get stored microsite after create');
        
        $this->assertEquals( $microsite_request['title'], $microsite->title );
        $this->assertEquals( $microsite_request['project'], $microsite->project_id );
        $this->assertEquals( $project_manager->id, $microsite->user_id );
        $this->assertEquals( $project_manager->institution_id, $microsite->institution_id );
        
        $page = $microsite->pages()->first();
        
        $this->assertNotNull( $page, "microsite pages is null or empty" );
        $this->assertEquals( $microsite_request['content']['en']['content'], $page->content, "Page content has not been stored" );
        $this->assertEquals( $microsite_request['content']['en']['title'], $page->title, "Page title has not been stored" );
        $this->assertEquals( $microsite_request['content']['en']['slug'], $page->slug, "Page slug has not been stored" );
        $this->assertEquals( 'en', $page->language, "Page language has not been stored" );

        $this->assertRedirectedToRoute('microsites.edit', ['id' => $microsite->id]);
        
    }
    
    /**
     * @dataProvider invalid_microsite_creation_request
     */
    public function testMicrositeStoreWithInvalidData( $request_data, $attribute, $error_type ){
        
        $project = factory('KlinkDMS\Project')->create();
        $project_manager = $project->manager()->first();
        
        \Session::start();
        
        $this->actingAs( $project_manager );
        
        
        $this->visit( route('microsites.create', ['project' => $project->id]));
        // $this->visit( route('projects.show', ['id' => $project->id ]) );
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id,
            'institution_id' => $project_manager->institution_id,
        ])->toArray();
        
        unset($microsite_request['project_id']);
        unset($microsite_request['institution_id']);
        unset($microsite_request['user_id']);
        
        $microsite_request['content'] = [
            'en' => [
                'title' => 'Example page',
                'slug' => 'Example page',
                'content' => 'Example page content',
            ]
        ];
        
        
        unset($microsite_request[$attribute]);
        $microsite_request = array_merge($microsite_request, $request_data);
        
        // var_dump($microsite_request);
        
        
        $microsite_request['_token'] = csrf_token();
        
        $this->post( route('microsites.store'), $microsite_request);
        
        // var_dump(\Session::all());
        // var_dump($this->response->getContent());
        
        if($error_type === 'authorize'){
            $this->see('Forbidden');
        }
        else {
            
            $this->assertSessionHasErrors( array($attribute) );
        
        }
        
    }
    
    public function testMicrositeStoreOnPojectWithExistingMicrosite(){
        
        $project = factory('KlinkDMS\Project')->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id,
            'institution_id' => $project_manager->institution_id,
        ])->toArray();
        
        unset($microsite_request['project_id']);
        unset($microsite_request['institution_id']);
        unset($microsite_request['user_id']);
        
        $microsite_request['content'] = [
            'en' => [
                'title' => 'Example page',
                'slug' => 'Example page',
                'content' => 'Example page content',
            ]
        ];
        
        \Session::start();
        
        $this->actingAs( $project_manager );
        
        $microsite_request['_token'] = csrf_token();
        
        $this->post( route('microsites.store'), $microsite_request);
        
        // double post the same things, expect an error
        $microsite_request['slug'] = 'another-slug';
        $this->post( route('microsites.store'), $microsite_request);
        
        $message = trans('microsites.errors.create', ['error' => trans('microsites.errors.create_already_exists', ['project' => $project->name])]);
        
        $this->assertSessionHasErrors(['error' => $message]);
        
    }
    
    
    public function testMicrositeEditFromProjectShowPage(){
        
        $project = factory('KlinkDMS\Project')->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
            'institution_id' => $project_manager->institution_id,
        ]);
        
        \Session::start();
        
        $this->actingAs( $project_manager );
        
        $this->visit( route('projects.show', ['id' => $project->id]));
        
        $this->click( 'microsite_edit' );
        
        $this->seePageIs( route( 'microsites.edit', ['id' => $microsite->id] ) ); 
        
    }
    
    public function testMicrositeUpdateWithValidData(){
        
        // TODO: invoke the Update route to test the request validation
        // TODO: check if redirected to microsites.show after creation
        // TODO: test what is in the database
        
        $this->markTestIncomplete( 'microsites Update with valid data' );
        
    }
    
    public function testMicrositeUpdateWithInvalidData( $request_data ){
        // TODO: invoke the Update route to test the request validation
        $this->markTestIncomplete( 'microsites Update with INvalid data' );
    }
    
    
    public function testMicrositeDeleteWithValidData(){
        
        $project = factory('KlinkDMS\Project')->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
            'institution_id' => $project_manager->institution_id,
        ]);
        
        \Session::start();
        
        $this->actingAs( $project_manager );
        
        $this->delete( route('microsites.destroy', [
                'id' => $microsite->id, 
                '_token' => csrf_token()])
             );        
        
        $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $this->response);
        
        $this->assertSessionHas('flash_message', trans('microsites.messages.deleted', ['title' => $microsite->title ]));
        
    }
    
    /**
     *
     * @dataProvider expected_auth_for_routes_provider
     */
    public function testMicrositeDeleteWithInValidData( $caps, $response_status){
        
        $user = $this->createUser( $caps );
        
        $project = factory('KlinkDMS\Project')->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
            'institution_id' => $project_manager->institution_id,
        ]);
        
        \Session::start();
        
        $this->actingAs( $user );
        
        $this->delete( route('microsites.destroy', [
                'id' => $microsite->id, 
                '_token' => csrf_token()])
             );
        
        if($response_status === 403){
            
            // var_dump( $this->response->original->name() );
            
            $this->assertViewName( 'errors.403' );
        }
        else {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $this->response);
            
            $this->assertSessionHasErrors([
                'error' => trans('microsites.errors.delete_forbidden', ['title' => $microsite->title ])
            ]);
        }

    }
    
        
}
