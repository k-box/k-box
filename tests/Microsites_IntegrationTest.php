<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KBox\Capability;
use Klink\DmsMicrosites\Microsite;
use Klink\DmsMicrosites\MicrositeContent;

class Microsites_IntegrationTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function expected_routes_provider()
    {
        return [
            [ 'microsites.slug', ['slug' => 'test'] ],
            [ 'microsites.index', [] ],
            [ 'microsites.show', ['id' => 1] ],
            [ 'microsites.create', [] ],
            [ 'microsites.store', [] ],
            [ 'microsites.edit', ['id' => 1] ],
            [ 'microsites.update', ['id' => 1] ],
            [ 'microsites.destroy', ['id' => 1] ]
        ];
    }
    
    public function microsite_routes_and_expected_page_provider_before_login()
    {
        return [
            [ 'visit', 'microsites.slug', ['slug' => 'test'], 200, 'microsites.slug' ],
            [ 'visit', 'microsites.index', [], 200, 'frontpage' ],
            [ 'visit', 'microsites.show', ['id' => 1], 200, 'microsites.show' ],
            [ 'visit', 'microsites.create', [], 200, 'frontpage' ],
            [ 'post', 'microsites.store', [], 302, 'frontpage' ],
            [ 'visit', 'microsites.edit', ['id' => 1], 200, 'frontpage' ],
            [ 'put', 'microsites.update', ['id' => 1], 302, 'frontpage' ],
            [ 'delete', 'microsites.destroy', ['id' => 1], 302, 'frontpage' ]
        ];
    }
    
    public function expected_auth_for_routes_provider()
    {
        return [
            [Capability::$ADMIN, 200],
            [Capability::$PROJECT_MANAGER_LIMITED, 200],
            [[Capability::MANAGE_KBOX], 403],
            [Capability::$PARTNER, 403],
            [[Capability::RECEIVE_AND_SEE_SHARE], 403],
        ];
    }
    
    public function invalid_microsite_creation_request()
    {
        // this parameters are the ones that will be manipulated to create an invalid request
        return [
            // request, attribute to be modified, kind of error expected
            [['project' => null], 'project', 'authorize'],
            [['project' => ''], 'project', 'authorize'],
            [['project' => 'string'], 'project', 'authorize'],
            [['project' => 125], 'project', 'authorize'],
            [['title' => null], 'title', 'validation'],
            [['title' => ''], 'title', 'validation'],
            [['title' => 125], 'title', 'validation'],
            [['slug' => 125], 'slug', 'validation'],
            [['slug' => '125'], 'slug', 'validation'],
            [['slug' => null], 'slug', 'validation'],
            [['slug' => ''], 'slug', 'validation'],
            [['slug' => 'create'], 'slug', 'validation'],
            [['slug' => 'create-best-performance'], 'slug', 'validation'],
            [['logo' => 'a'], 'logo', 'validation'],
            [['logo' => 'ab'], 'logo', 'validation'],
            [['logo' => 'abc'], 'logo', 'validation'],
            [['logo' => 'abcd'], 'logo', 'validation'],
            [['logo' => 'helpme'], 'logo', 'validation'],
            [['logo' => 'http://helpme.com/'], 'logo', 'validation'],
            [['logo' => ['a']], 'logo', 'validation'],
            [['default_language' => []], 'default_language', 'validation'],
            [['default_language' => ''], 'default_language', 'validation'],
            [['default_language' => null], 'default_language', 'validation'],
            [['default_language' => 5], 'default_language', 'validation'],
            [['default_language' => 'lang'], 'default_language', 'validation'],
            [['default_language' => '5'], 'default_language', 'validation'],
            [['default_language' => 'l'], 'default_language', 'validation'],
            [['default_language' => '51'], 'default_language', 'validation'],
            [['default_language' => 'l1'], 'default_language', 'validation'],
            [['content' => 'l1'], 'content', 'validation'],
            [['content' => null], 'content', 'validation'],
            [['content' => ''], 'content', 'validation'],
            [['menu' => 'l1'], 'menu', 'validation'],
            [['menu' => null], 'menu', 'validation'],
            [['menu' => ''], 'menu', 'validation'],
            [['hero_image' => 'a'], 'hero_image', 'validation'],
            [['hero_image' => 'ab'], 'hero_image', 'validation'],
            [['hero_image' => 'abc'], 'hero_image', 'validation'],
            [['hero_image' => 'abcd'], 'hero_image', 'validation'],
            [['hero_image' => 'helpme'], 'hero_image', 'validation'],
            [['hero_image' => 'http://helpme.com'], 'hero_image', 'validation'],
            [['hero_image' => ['a']], 'hero_image', 'validation'],
        ];
    }
    
    public function language_switch_provider()
    {
        return [
            ['en', 'ru'],
            ['ru', 'en'],
            ['en', 'en'],
            ['ru', 'ru'],
        ];
    }
     
    /**
     * Test the expected microsites routes are available
     *
     * @dataProvider expected_routes_provider
     * @return void
     */
    public function testMicrositeRoutesExistence($route_name, $parameters)
    {
        // you will see InvalidArgumentException if the route is not defined
        
        route($route_name, $parameters);
        
        $this->assertTrue(true, "Test complete without exceptions");
    }
    
    /**
     * @dataProvider microsite_routes_and_expected_page_provider_before_login
     */
    public function testMicrositeRouteAuthentication_NoLogin($method, $route, $parameters, $return_code, $expected_route)
    {
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'id' => 1,
        ]);

        $this->{$method}(route($route, $parameters));
        
        $this->assertResponseStatus($return_code);
        
        if ($return_code === 200) {
            $this->seePageIs(route($expected_route, $expected_route === 'frontpage' ? [] : $parameters));
        }
    }
    
    /**
     * Test if the Project show page of a specific project has the microsite section on the UI
     */
    public function testMicrositeCreateActionsOnProjectShowPage()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $this->actingAs($project->manager()->first());
        
        $this->visit(route('projects.show', ['id' => $project->id]))
            ->see('Microsite')
            ->see(trans('microsites.actions.create'));
            
        $this->click('microsite_create');
        
        $this->seePageIs(route('microsites.create', ['project' => $project->id]));
        
        $this->see(trans('microsites.pages.create', ['project' => $project->name]));
        
        $this->see(trans('microsites.actions.publish'));
        
        $this->assertViewHas('pagetitle');
        $this->assertViewHas('project');
    }
    
    public function testMicrositeManageActionsVisibilityOnProjectShowPage()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id
        ]);
        
        $this->actingAs($project_manager);
        
        $this->visit(route('projects.show', ['id' => $project->id]))
            ->see('Microsite')
            ->see(trans('microsites.actions.delete'))
            ->see(trans('microsites.actions.view_site'))
            ->see(trans('microsites.actions.edit'));
    }
    
    public function testMicrositeCreateInvokedWithoutProjectParameter()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        \Session::start();
        
        $this->actingAs($project_manager);
        
        $this->visit(route('projects.show', ['id' => $project->id ]));
        
        $this->visit(route('microsites.create'));
        
        $this->seePageIs(route('projects.show', ['id' => $project->id ]));
        
        $this->see(trans('microsites.errors.create_no_project'));
    }
    
    public function testMicrositeCreateInvokedWithoutUserAffiliation()
    {
        $user = $this->createAdminUser();
        
        $project = factory(\KBox\Project::class)->create(['user_id' => $user->id]);
        
        \Session::start();
        
        $this->actingAs($user);
        
        $this->visit(route('projects.show', ['id' => $project->id ]));
        
        $this->visit(route('microsites.create', ['project' => $project->id]));
        
        $this->seePageIs(route('microsites.create', ['project' => $project->id ]));
        
        $this->dontSee(trans('microsites.errors.user_not_affiliated_to_an_institution'));
    }
    
    public function testMicrositeCreateOnProjectWithExistingMicrosite()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id
        ]);
        
        $this->actingAs($project_manager);
        
        $this->visit(route('projects.show', ['id' => $project->id ]));
        
        $this->visit(route('microsites.create', ['project' => $project->id]));
        
        $this->seePageIs(route('projects.show', ['id' => $project->id ]));
        
        $this->see(trans('microsites.errors.create_already_exists', ['project' => $project->name]));
    }
    
    public function testMicrositeStoreWithValidData()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id
        ])->toArray();
        
        unset($microsite_request['project_id']);
        unset($microsite_request['user_id']);
        
        $microsite_request['content'] = [
            'en' => [
                'title' => 'Example page',
                'slug' => 'Example page',
                'content' => 'Example page content',
            ]
        ];
        
        \Session::start();
        
        $this->actingAs($project_manager);
        
        $this->post(route('microsites.store'), $microsite_request);
        
        $microsite = Microsite::where('slug', $microsite_request['slug'])->first();
        
        $this->assertNotNull($microsite, 'Cannot get stored microsite after create');
        
        $this->assertEquals($microsite_request['title'], $microsite->title);
        $this->assertEquals($microsite_request['project'], $microsite->project_id);
        $this->assertEquals($project_manager->id, $microsite->user_id);
        
        $page = $microsite->pages()->first();
        
        $this->assertNotNull($page, "microsite pages is null or empty");
        $this->assertEquals($microsite_request['content']['en']['content'], $page->content, "Page content has not been stored");
        $this->assertEquals($microsite_request['content']['en']['title'], $page->title, "Page title has not been stored");
        $this->assertEquals($microsite_request['content']['en']['slug'], $page->slug, "Page slug has not been stored");
        $this->assertEquals('en', $page->language, "Page language has not been stored");

        $this->assertRedirectedToRoute('microsites.edit', ['id' => $microsite->id]);
    }
    
    public function testMicrositeStoreWithValidData_NoLogo()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id,
        ])->toArray();
        
        unset($microsite_request['project_id']);
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
        
        $this->actingAs($project_manager);
        
        $this->post(route('microsites.store'), $microsite_request);
        
        $microsite = Microsite::where('slug', $microsite_request['slug'])->first();
        
        $this->assertNotNull($microsite, 'Cannot get stored microsite after create');
        
        $this->assertEquals($microsite_request['title'], $microsite->title);
        $this->assertEquals($microsite_request['project'], $microsite->project_id);
        $this->assertEquals($project_manager->id, $microsite->user_id);
        
        $page = $microsite->pages()->first();
        
        $this->assertNotNull($page, "microsite pages is null or empty");
        $this->assertEquals($microsite_request['content']['en']['content'], $page->content, "Page content has not been stored");
        $this->assertEquals($microsite_request['content']['en']['title'], $page->title, "Page title has not been stored");
        $this->assertEquals($microsite_request['content']['en']['slug'], $page->slug, "Page slug has not been stored");
        $this->assertEquals('en', $page->language, "Page language has not been stored");

        $this->assertRedirectedToRoute('microsites.edit', ['id' => $microsite->id]);
    }
    
    /**
     * @dataProvider invalid_microsite_creation_request
     */
    public function testMicrositeStoreWithInvalidData($request_data, $attribute, $error_type)
    {
        $project = factory(\KBox\Project::class)->create();
        $project_manager = $project->manager()->first();
        
        \Session::start();
        
        $this->actingAs($project_manager);
        
        $this->visit(route('microsites.create', ['project' => $project->id]));
        // $this->visit( route('projects.show', ['id' => $project->id ]) );
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id
        ])->toArray();
        
        unset($microsite_request['project_id']);
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
        
        $microsite_request['_token'] = csrf_token();
        
        $this->post(route('microsites.store'), $microsite_request);
        
        if ($error_type === 'authorize') {
            $this->see(trans('errors.403_text'));
            $this->dontSee('errors.403_text');
        } elseif (! is_null($error_type)) {
            $this->assertSessionHasErrors([$attribute]);
        } else {
            $this->assertFalse($this->app['session.store']->has('errors'), "expecting no error, but found one");
        }
    }
    
    /**
     *
     * @dataProvider language_switch_provider
     */
    public function testMicrositeLanguageSwitch($default_lang, $switch_to)
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id,
            'default_language' => $default_lang
        ])->toArray();
        
        unset($microsite_request['project_id']);
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
        
        $this->actingAs($project_manager);
        
        $this->post(route('microsites.store'), $microsite_request);
        
        $microsite = Microsite::where('slug', $microsite_request['slug'])->first();
        
        $this->assertNotNull($microsite, 'Cannot get stored microsite after create');
        
        $this->assertEquals($microsite_request['title'], $microsite->title);
        $this->assertEquals($microsite_request['project'], $microsite->project_id);
        $this->assertEquals($project_manager->id, $microsite->user_id);
        
        $page_count = $microsite->pages()->count();
        
        $this->assertEquals(2, $page_count);
        
        // visit the page and test if $default_lang version is showed
        $this->visit(route('microsites.slug', ['slug' => $microsite_request['slug']]));
        
        $this->see($microsite_request['content'][$default_lang]['content']);
        
        // click on $switch_to lang switch and test if $switch_to version is showed
        
        $this->click($switch_to);
        
        $this->see($microsite_request['content'][$switch_to]['content']);
    }
    
    public function testMicrositeStoreOnPojectWithExistingMicrosite()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id,
        ])->toArray();
        
        unset($microsite_request['project_id']);
        unset($microsite_request['user_id']);
        
        $microsite_request['content'] = [
            'en' => [
                'title' => 'Example page',
                'slug' => 'Example page',
                'content' => 'Example page content',
            ]
        ];
        
        \Session::start();
        
        $this->actingAs($project_manager);
        
        $this->post(route('microsites.store'), $microsite_request);

        // double post the same things, expect an error
        $microsite_request['slug'] = 'another-slug';
        $this->post(route('microsites.store'), $microsite_request);
        
        $message = trans('microsites.errors.create', ['error' => trans('microsites.errors.create_already_exists', ['project' => $project->name])]);
        
        $this->assertSessionHasErrors(['error' => $message]);
    }
    
    public function testMicrositeEditFromProjectShowPage()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
        ]);
        
        \Session::start();
        
        $this->actingAs($project_manager);
        
        $this->visit(route('projects.show', ['id' => $project->id]));
        
        $this->click('microsite_edit');
        
        $this->seePageIs(route('microsites.edit', ['id' => $microsite->id]));
    }
    
    public function testMicrositeUpdateWithValidData()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project->id,
            'project' => $project->id,
            'user_id' => $project_manager->id,
        ])->toArray();
        
        unset($microsite_request['project_id']);
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
        
        $this->actingAs($project_manager);
        
        $this->post(route('microsites.store'), $microsite_request);

        $microsite = Microsite::where('slug', $microsite_request['slug'])->first();
        
        $this->assertNotNull($microsite, 'Cannot get stored microsite after create');
        
        $en_page = $microsite->contents()->type(MicrositeContent::TYPE_PAGE)->language('en')->first();
        $ru_page = $microsite->contents()->type(MicrositeContent::TYPE_PAGE)->language('ru')->first();
        
        // now let's update every field and check if the microsite is saved
        
        $microsite_update_request = [
            'title' => 'Changed title of the microsite',
            'slug' => $microsite_request['slug'].'-updated',
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
        
        $this->put(route('microsites.update', ['id' => $microsite->id]), $microsite_update_request);
        
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
        
        $this->assertNotNull($en_page, "microsite EN page is null or empty");
        $this->assertEquals($microsite_update_request['content']['en']['content'], $en_page->content, "EN Page content has not been stored");
        $this->assertEquals($microsite_update_request['content']['en']['title'], $en_page->title, "EN Page title has not been stored");
        $this->assertEquals($microsite_update_request['content']['en']['slug'], $en_page->slug, "EN Page slug has not been stored");
        $this->assertEquals('en', $en_page->language, "EN Page language has not been stored");
        
        $this->assertNotNull($ru_page, "microsite RU page is null or empty");
        $this->assertEquals($microsite_update_request['content']['ru']['content'], $ru_page->content, "RU Page content has not been stored");
        $this->assertEquals($microsite_update_request['content']['ru']['title'], $ru_page->title, "RU Page title has not been stored");
        $this->assertEquals($microsite_update_request['content']['ru']['slug'], $ru_page->slug, "RU Page slug has not been stored");
        $this->assertEquals('ru', $ru_page->language, "RU Page language has not been stored");
    }
    
    public function testMicrositeDeleteWithValidData()
    {
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
        ]);
        
        \Session::start();
        
        $this->actingAs($project_manager);
        
        $this->delete(
            route('microsites.destroy', [
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
    public function testMicrositeDeleteWithInValidData($caps, $response_status)
    {
        $user = $this->createUser($caps);
        
        $project = factory(\KBox\Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
        ]);
        
        \Session::start();
        
        $this->actingAs($user);
        
        $this->delete(
            route('microsites.destroy', [
                'id' => $microsite->id,
                '_token' => csrf_token()])
        );
        
        if ($response_status === 403) {
            $this->assertViewName('errors.403');
        } else {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $this->response);
            
            $this->assertSessionHasErrors([
                'error' => trans('microsites.errors.delete_forbidden', ['title' => $microsite->title ])
            ]);
        }
    }

    public function test_html_can_be_used_in_microsite_content()
    {
        $content = MicrositeContent::make([
            'language' => 'en',
            'type' => MicrositeContent::TYPE_PAGE,
            'user_id' => 1,
            'content' => 'This contains a <strong>bold statement</strong>. <div>A div</div> and an <img src="http://localhost/image.png">',
        ]);

        $rendered_content = app()->make('micrositeparser')->toHtml($content);

        $this->assertEquals('<p>This contains a <strong>bold statement</strong>. <div>A div</div> and an <img src="http://localhost/image.png"></p>', trim($rendered_content));
    }

    public function test_scripts_cannot_be_used_in_microsite_content()
    {
        $content = MicrositeContent::make([
            'language' => 'en',
            'type' => MicrositeContent::TYPE_PAGE,
            'user_id' => 1,
            'content' => 'This contains a <strong>bold statement</strong>.<script>alert(\'\')</script> <div>A div</div> and an <img src="http://localhost/image.png">',
        ]);

        $rendered_content = app()->make('micrositeparser')->toHtml($content);

        $this->assertEquals('<p>This contains a <strong>bold statement</strong>.alert(\'\') <div>A div</div> and an <img src="http://localhost/image.png"></p>', trim($rendered_content));
    }
}
