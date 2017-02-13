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
use Klink\DmsMicrosites\MicrositeContent;


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
			array( 'visit', 'microsites.slug', ['slug' => 'test'], 200, 'microsites.slug' ),
			array( 'visit', 'microsites.index', [], 200, 'auth.login' ),
			array( 'visit', 'microsites.show', ['id' => 1], 200, 'microsites.show' ),
			array( 'visit', 'microsites.create', [], 200, 'auth.login' ),
			array( 'post', 'microsites.store', [], 302, 'auth.login' ),
			array( 'visit', 'microsites.edit', ['id' => 1], 200, 'auth.login' ),
			array( 'put', 'microsites.update', ['id' => 1], 302, 'auth.login' ),
			array( 'delete', 'microsites.destroy', ['id' => 1], 302, 'auth.login' )
		);
	}
    
    public function expected_auth_for_routes_provider(){
		
		return array( 
			array(Capability::$ADMIN, 200),
			array(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH, 200),
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
			array(array('slug' => 'create'), 'slug', 'validation'),
			array(array('slug' => 'create-best-performance'), 'slug', 'validation'),
			array(array('logo' => 'a'), 'logo', 'validation'),
			array(array('logo' => 'ab'), 'logo', 'validation'),
			array(array('logo' => 'abc'), 'logo', 'validation'),
			array(array('logo' => 'abcd'), 'logo', 'validation'),
			array(array('logo' => 'helpme'), 'logo', 'validation'),
			array(array('logo' => 'http://helpme.com/'), 'logo', 'validation'),
			array(array('logo' => array('a')), 'logo', 'validation'),
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
            array(array('hero_image' => 'a'), 'hero_image', 'validation'),
            array(array('hero_image' => 'ab'), 'hero_image', 'validation'),
            array(array('hero_image' => 'abc'), 'hero_image', 'validation'),
            array(array('hero_image' => 'abcd'), 'hero_image', 'validation'),
			array(array('hero_image' => 'helpme'), 'hero_image', 'validation'),
			array(array('hero_image' => 'http://helpme.com'), 'hero_image', 'validation'),
			array(array('hero_image' => array('a')), 'hero_image', 'validation'),
		);
	}
    
    public function language_switch_provider(){
		
		return array( 
			array('en', 'ru'),
			array('ru', 'en'),
			array('en', 'en'),
			array('ru', 'ru'),
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
    public function testMicrositeRouteAuthentication_NoLogin( $method, $route, $parameters, $return_code, $expected_route ){
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'id' => 1,
        ]);

		$this->{$method}( route( $route, $parameters ) );
        
        $this->assertResponseStatus($return_code);
        
        if($return_code === 200){
            $this->seePageIs( route( $expected_route, $expected_route === 'auth.login' ? [] : $parameters ) );
        }
        
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
            'institution_id' => $institution
        ]);
        
        $project = factory('KlinkDMS\Project')->create([
            'user_id' => $user->id
        ]);
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project->user_id,
            'institution_id' => $project->manager->institution_id,
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
    
    public function testMicrositeCreateInvokedWithoutUserAffiliation(){
        
        $user = $this->createAdminUser(['institution_id' => null]);
        
        $project = factory('KlinkDMS\Project')->create(['user_id' => $user->id]);
        
        \Session::start();
        
        $this->actingAs( $user );
        
        $this->visit( route('projects.show', ['id' => $project->id ]) );
        
        $this->visit( route('microsites.create', ['project' => $project->id]) );
        
        $this->seePageIs( route('projects.show', ['id' => $project->id ]) );
        
        $this->see( trans('microsites.errors.user_not_affiliated_to_an_institution') );
        
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
    
    public function testMicrositeStoreWithValidData_NoLogo(){
        
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
        unset($microsite_request['logo']);
        
        $microsite_request['content'] = [
            'en' => [
                'title' => 'Example page',
                'slug' => 'Example page',
                'content' => 'Example page content',
            ]
        ];
        
        \Session::start();
        
        $this->actingAs( $project_manager );
        
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
        else if(!is_null($error_type)) {
            
            $this->assertSessionHasErrors( array($attribute) );
        
        }
        else {
            $this->assertFalse($this->app['session.store']->has('errors'), "expecting no error, but found one");
        }
        
    }
    
    /**
     *
     * @dataProvider language_switch_provider
     */
    public function testMicrositeLanguageSwitch($default_lang, $switch_to){
        
        $project = factory('KlinkDMS\Project')->create();
        
        $project_manager = $project->manager()->first();
        
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id,
            'institution_id' => $project_manager->institution_id,
            'default_language' => $default_lang
        ])->toArray();
        
        unset($microsite_request['project_id']);
        unset($microsite_request['institution_id']);
        unset($microsite_request['user_id']);
        unset($microsite_request['logo']);
        
        $microsite_request['content'] = [
            'en' => [
                'title' => 'EN Example page',
                'slug' => 'en-example-page',
                'content' => 'English Example page content',
            ],
            'ru' => [
                'title' => 'RU Example page',
                'slug' => 'ru-example-page',
                'content' => 'Russian Example page content',
            ]
        ];
        
        \Session::start();
        
        $this->actingAs( $project_manager );
        
        $this->post( route('microsites.store'), $microsite_request);
        
        $microsite = Microsite::where('slug', $microsite_request['slug'])->first();
        
        $this->assertNotNull($microsite, 'Cannot get stored microsite after create');
        
        $this->assertEquals( $microsite_request['title'], $microsite->title );
        $this->assertEquals( $microsite_request['project'], $microsite->project_id );
        $this->assertEquals( $project_manager->id, $microsite->user_id );
        $this->assertEquals( $project_manager->institution_id, $microsite->institution_id );
        
        $page_count = $microsite->pages()->count();
        
        $this->assertEquals(2, $page_count);
        
        // visit the page and test if $default_lang version is showed
        $this->visit( route('microsites.slug', ['slug' => $microsite_request['slug']]) );
        
        $this->see($microsite_request['content'][$default_lang]['content']);
        
        // click on $switch_to lang switch and test if $switch_to version is showed
        
        $this->click($switch_to);
        
        $this->see($microsite_request['content'][$switch_to]['content']);
        
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
        unset($microsite_request['logo']);
        
        $microsite_request['content'] = [
            'en' => [
                'title' => 'EN Example page',
                'slug' => 'en-example-page',
                'content' => 'English Example page content',
            ],
            'ru' => [
                'title' => 'RU Example page',
                'slug' => 'ru-example-page',
                'content' => 'Russian Example page content',
            ]
        ];
        
        \Session::start();
        
        $this->actingAs( $project_manager );
        
        $this->post( route('microsites.store'), $microsite_request);
        
        $microsite = Microsite::where('slug', $microsite_request['slug'])->first();
        
        $this->assertNotNull($microsite, 'Cannot get stored microsite after create');
        
        $en_page = $microsite->contents()->type(MicrositeContent::TYPE_PAGE)->language('en')->first();
        $ru_page = $microsite->contents()->type(MicrositeContent::TYPE_PAGE)->language('ru')->first();
        
        // now let's update every field and check if the microsite is saved
        
        $microsite_update_request = [
            'title' => 'Changed title of the microsite',
            'slug' => $microsite_request['slug'] . '-updated',
            'description' => 'New description of the microsite',
            'logo' => 'https://something.com/logo.png',
            'hero_image' => 'https://something.com/hero.png',
            'default_language' => 'ru',
            'content' => [
                'en' => [
                    'id' => $en_page->id,
                    'title' => 'Updated EN title',
                    'slug' => 'en-example-page',
                    'content' => 'Updated English Example page content',
                ],
                'ru' => [
                    'id' => $ru_page->id,
                    'title' => 'Updated RU title',
                    'slug' => 'ru-example-page',
                    'content' => 'Updated Russian Example page content',
                ]
            ]
        ];
        
        $this->put( route('microsites.update', ['id' => $microsite->id]), $microsite_update_request);
        
        
        $microsite_after_update = Microsite::where('slug', $microsite_update_request['slug'])->first();
        
        $this->assertNotNull($microsite_after_update, 'Cannot get stored microsite after update');
        
        $this->assertEquals($microsite_after_update->id, $microsite->id);
        $this->assertEquals($microsite_update_request['default_language'], $microsite_after_update->default_language);
        $this->assertEquals($microsite_update_request['title'], $microsite_after_update->title);
        $this->assertEquals($microsite_update_request['slug'], $microsite_after_update->slug);
        $this->assertEquals($microsite_update_request['description'], $microsite_after_update->description);
        $this->assertEquals($microsite_update_request['logo'], $microsite_after_update->logo);
        $this->assertEquals($microsite_update_request['hero_image'], $microsite_after_update->hero_image);
        
        $pages_count = $microsite_after_update->pages()->count();
        $this->assertEquals(2, $pages_count);
        
        $en_page = $microsite_after_update->contents()->type(MicrositeContent::TYPE_PAGE)->language('en')->first();
        $ru_page = $microsite_after_update->contents()->type(MicrositeContent::TYPE_PAGE)->language('ru')->first();
        
        $this->assertNotNull( $en_page, "microsite EN page is null or empty" );
        $this->assertEquals( $microsite_update_request['content']['en']['content'], $en_page->content, "EN Page content has not been stored" );
        $this->assertEquals( $microsite_update_request['content']['en']['title'], $en_page->title, "EN Page title has not been stored" );
        $this->assertEquals( $microsite_update_request['content']['en']['slug'], $en_page->slug, "EN Page slug has not been stored" );
        $this->assertEquals( 'en', $en_page->language, "EN Page language has not been stored" );
        
        $this->assertNotNull( $ru_page, "microsite RU page is null or empty" );
        $this->assertEquals( $microsite_update_request['content']['ru']['content'], $ru_page->content, "RU Page content has not been stored" );
        $this->assertEquals( $microsite_update_request['content']['ru']['title'], $ru_page->title, "RU Page title has not been stored" );
        $this->assertEquals( $microsite_update_request['content']['ru']['slug'], $ru_page->slug, "RU Page slug has not been stored" );
        $this->assertEquals( 'ru', $ru_page->language, "RU Page language has not been stored" );
        
    }
    
    public function testMicrositeUpdateWithInvalidData( /*$request_data*/ ){
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
