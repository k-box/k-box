<?php

namespace Tests\Feature;

use KBox\Flags;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use Klink\DmsMicrosites\Microsite;
use Klink\DmsMicrosites\MicrositeContent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\User;

class Microsites_IntegrationTest extends TestCase
{
    use DatabaseTransactions;

    private function enableMicrositeFlag()
    {
        Flags::enable(Flags::MICROSITES);
    }
    
    private function disableMicrositeFlag()
    {
        Flags::disable(Flags::MICROSITES);
    }
    
    public function expected_routes_provider()
    {
        return [
            [ 'microsites.slug', ['slug' => 'test'] ],
            [ 'microsites.index', [] ],
            [ 'microsites.show', ['microsite' => 1] ],
            [ 'microsites.create', [] ],
            [ 'microsites.store', [] ],
            [ 'microsites.edit', ['microsite' => 1] ],
            [ 'microsites.update', ['microsite' => 1] ],
            [ 'microsites.destroy', ['microsite' => 1] ]
        ];
    }
    
    public function microsite_routes_and_expected_page_provider_before_login()
    {
        return [
            [ 'get', 'microsites.slug', ['slug' => 'test'], 200, 'sites::site.site' ],
            [ 'get', 'microsites.index', [], 302, 'frontpage' ],
            [ 'get', 'microsites.show', ['microsite' => 1], 200, 'sites::site.site' ],
            [ 'get', 'microsites.create', [], 302, 'frontpage' ],
            [ 'post', 'microsites.store', [], 302, 'frontpage' ],
            [ 'get', 'microsites.edit', ['microsite' => 1], 302, 'frontpage' ],
            [ 'put', 'microsites.update', ['microsite' => 1], 302, 'frontpage' ],
            [ 'delete', 'microsites.destroy', ['microsite' => 1], 302, 'frontpage' ]
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
            [['slug' => 'project-hello-125'], 'slug', 'validation'],
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
    public function test_microsites_route_not_found_if_flag_disabled($method, $route, $parameters, $return_code, $expected_route)
    {
        $this->disableMicrositeFlag();

        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'id' => 1,
        ]);

        $response = $this->{$method}(route($route, $parameters));

        $response->assertStatus($return_code);
        if ($return_code === 200) {
            $response->assertErrorView(404);
        }
    }

    /**
     * @dataProvider microsite_routes_and_expected_page_provider_before_login
     */
    public function testMicrositeRouteAuthentication_NoLogin($method, $route, $parameters, $return_code, $expected_view)
    {
        $this->enableMicrositeFlag();

        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'id' => 1,
            'slug' => 'test'
        ]);

        $response = $this->{$method}(route($route, $parameters));
        
        $response->assertStatus($return_code);
        
        if ($return_code === 200) {
            $response->assertViewIs($expected_view);
        }
    }
    
    public function test_microsite_section_on_project_details_not_present_when_flag_disabled()
    {
        $this->disableMicrositeFlag();

        $project = factory(Project::class)->create();
        
        $this->actingAs($project->manager()->first());
        
        $response = $this->get(route('documents.projects.show', $project->id));

        $response->assertDontSee('Microsite');
        $response->assertDontSee(trans('microsites.actions.create'));
        $response->assertDontSee(route('microsites.create', ['project' => $project->id]));
    }
    
    public function testMicrositeCreateInvokedWithoutUserAffiliation()
    {
        $this->enableMicrositeFlag();

        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$ADMIN);
        
        $project = factory(Project::class)->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)
            ->from(route('projects.show', $project->id))
            ->get(route('microsites.create', ['project' => $project->id]));
        
        $response->assertViewIs('sites::create');
    }
    
    public function testMicrositeCreateOnProjectWithExistingMicrosite()
    {
        $this->enableMicrositeFlag();

        $project = factory(Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id
        ]);
        
        $response = $this->actingAs($project_manager)
            ->from(route('projects.show', $project->id))
            ->get(route('microsites.create', ['project' => $project->id]));

        $response->assertRedirect(route('projects.show', $project->id));
        $response->assertSessionHasErrors([
            'error' => trans('microsites.errors.create_already_exists', ['project' => $project->name])
        ]);
    }
    
    public function testMicrositeStoreWithValidData()
    {
        $this->enableMicrositeFlag();

        $project = factory(Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = $this->createValidMicrositeRequest($project->id, $project_manager->id);
        
        unset($microsite_request['project_id']);
        unset($microsite_request['user_id']);
        
        $response = $this->actingAs($project_manager)
            ->post(route('microsites.store'), $microsite_request);
            
        $microsite = Microsite::where('slug', $microsite_request['slug'])->first();
        
        $this->assertNotNull($microsite, 'Cannot get stored microsite after create');
        $this->assertEquals($microsite_request['title'], $microsite->title);
        $this->assertEquals($microsite_request['project'], $microsite->project_id);
        $this->assertEquals($project_manager->id, $microsite->user_id);
        
        $response->assertRedirect(route('microsites.edit', $microsite->id));
        
        $page = $microsite->pages()->first();
        
        $this->assertNotNull($page, "microsite pages is null or empty");
        $this->assertEquals($microsite_request['content']['en']['content'], $page->content, "Page content has not been stored");
        $this->assertEquals($microsite_request['content']['en']['title'], $page->title, "Page title has not been stored");
        $this->assertEquals($microsite_request['content']['en']['slug'], $page->slug, "Page slug has not been stored");
        $this->assertEquals('en', $page->language, "Page language has not been stored");
    }
    
    public function testMicrositeStoreWithValidData_NoLogo()
    {
        $this->enableMicrositeFlag();

        $project = factory(Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = $this->createValidMicrositeRequest($project->id, $project_manager->id);
        
        unset($microsite_request['project_id']);
        unset($microsite_request['user_id']);
        unset($microsite_request['logo']);
        
        $response = $this->actingAs($project_manager)
            ->post(route('microsites.store'), $microsite_request);
        
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

        $response->assertRedirect(route('microsites.edit', $microsite->id));
    }
    
    /**
     * @dataProvider invalid_microsite_creation_request
     */
    public function testMicrositeStoreWithInvalidData($request_data, $attribute, $error_type)
    {
        $this->enableMicrositeFlag();

        $project = factory(Project::class)->create();
        $project_manager = $project->manager()->first();
        
        $this->actingAs($project_manager);
        
        $microsite_request = $this->createValidMicrositeRequest($project->id, $project_manager->id);
        
        unset($microsite_request[$attribute]);
        $microsite_request = array_merge($microsite_request, $request_data);
        
        $microsite_request['_token'] = csrf_token();
        
        $response = $this->post(route('microsites.store'), $microsite_request);
        
        if ($error_type === 'authorize') {
            $response->assertSee(trans('errors.403_text'), false);
            $response->assertDontSee('errors.403_text');
        } elseif (! is_null($error_type)) {
            $response->assertSessionHasErrors([$attribute]);
        } else {
            $response->assertSessionDoesntHaveErrors();
        }
    }
    
    public function testMicrositeStoreOnPojectWithExistingMicrosite()
    {
        $this->enableMicrositeFlag();

        $project = factory(Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = $this->createValidMicrositeRequest($project->id, $project_manager->id);

        $this->actingAs($project_manager);
        
        $this->post(route('microsites.store'), $microsite_request);

        // double post the same things, expect an error
        $microsite_request['slug'] = 'another-slug';
        $response = $this->post(route('microsites.store'), $microsite_request);
        
        $message = trans('microsites.errors.create', ['error' => trans('microsites.errors.create_already_exists', ['project' => $project->name])]);
        
        $response->assertSessionHasErrors(['error' => $message]);
    }
    
    public function testMicrositeEditFromProjectShowPage()
    {
        $this->enableMicrositeFlag();

        $project = factory(Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
        ]);
        
        $response = $this->actingAs($project_manager)
            ->get(route('microsites.edit', $microsite->id));
        
        $response->assertOk();
        $response->assertViewIs('sites::edit');
        $response->assertViewHas('microsite', $microsite);
    }
    
    public function testMicrositeUpdateWithValidData()
    {
        $this->enableMicrositeFlag();

        $project = factory(Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite_request = $this->createValidMicrositeRequest($project->id, $project_manager->id);
        
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
        
        $this->put(route('microsites.update', $microsite->id), $microsite_update_request);
        
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
        $this->enableMicrositeFlag();

        $project = factory(Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
        ]);
        
        $this->actingAs($project_manager);
        
        $response = $this->delete(
            route('microsites.destroy', [
                'microsite' => $microsite->id,
                '_token' => csrf_token()])
        );
        
        $response->assertInstanceOf('Illuminate\Http\RedirectResponse');
        $response->assertSessionHas('flash_message', trans('microsites.messages.deleted', ['title' => $microsite->title ]));
    }
    
    /**
     *
     * @dataProvider expected_auth_for_routes_provider
     */
    public function testMicrositeDeleteWithInValidData($caps, $response_status)
    {
        $this->enableMicrositeFlag();

        $user = tap(factory(User::class)->create())->addCapabilities($caps);
        
        $project = factory(Project::class)->create();
        
        $project_manager = $project->manager()->first();
        
        $microsite = factory('Klink\DmsMicrosites\Microsite')->create([
            'project_id' => $project->id,
            'user_id' => $project_manager->id,
        ]);
        
        $response = $this->actingAs($user)
            ->delete(
                route('microsites.destroy', [
                    'microsite' => $microsite->id,
                    '_token' => csrf_token()])
            );
        
        if ($response_status === 403) {
            $response->assertErrorView('403');
        } else {
            $response->assertInstanceOf('Illuminate\Http\RedirectResponse');
            
            $response->assertSessionHasErrors([
                'error' => trans('microsites.errors.delete_forbidden', ['title' => $microsite->title ])
            ]);
        }
    }

    public function test_html_can_be_used_in_microsite_content()
    {
        $this->enableMicrositeFlag();

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
        $this->enableMicrositeFlag();

        $content = MicrositeContent::make([
            'language' => 'en',
            'type' => MicrositeContent::TYPE_PAGE,
            'user_id' => 1,
            'content' => 'This contains a <strong>bold statement</strong>.<script>alert(\'\')</script> <div>A div</div> and an <img src="http://localhost/image.png">',
        ]);

        $rendered_content = app()->make('micrositeparser')->toHtml($content);

        $this->assertEquals('<p>This contains a <strong>bold statement</strong>.alert(\'\') <div>A div</div> and an <img src="http://localhost/image.png"></p>', trim($rendered_content));
    }

    private function createValidMicrositeRequest($project_id, $user_id)
    {
        $microsite_request = factory('Klink\DmsMicrosites\Microsite')->make([
            'project_id' => $project_id,
            'project' => $project_id,
            'user_id' => $user_id
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

        return $microsite_request;
    }
}
