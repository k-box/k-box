<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProjectAvatarsTest extends TestCase
{
    use DatabaseTransactions;
    
    public function expected_routes_provider()
    {
        return [
            [ 'projects.avatar.index', ['id' => 1] ],
            [ 'projects.avatar.store', ['id' => 1] ],
            [ 'projects.avatar.destroy', ['id' => 1] ]
        ];
    }
    
    public function routes_and_capabilities_provider()
    {
        return [
            [ Capability::$ADMIN, 'projects.avatar.index', 200 ],
            [ Capability::$ADMIN, 'projects.avatar.store', 403 ],
            [ Capability::$ADMIN, 'projects.avatar.destroy', 403 ],
            [ Capability::$PROJECT_MANAGER_LIMITED, 'projects.avatar.index', 200 ],
            [ Capability::$PROJECT_MANAGER_LIMITED, 'projects.avatar.store', 403 ],
            [ Capability::$PROJECT_MANAGER_LIMITED, 'projects.avatar.destroy', 403 ],
            [ Capability::$PROJECT_MANAGER, 'projects.avatar.index', 200 ],
            [ Capability::$PROJECT_MANAGER, 'projects.avatar.store', 403 ],
            [ Capability::$PROJECT_MANAGER, 'projects.avatar.destroy', 403 ],
            [ [Capability::MANAGE_KBOX], 'projects.avatar.index',  403 ],
            [ [Capability::MANAGE_KBOX], 'projects.avatar.store',  403 ],
            [ [Capability::MANAGE_KBOX], 'projects.avatar.destroy', 403 ],
            [ Capability::$PARTNER, 'projects.avatar.store', 403 ],
            [ Capability::$PARTNER, 'projects.avatar.destroy', 403 ],
            [ [Capability::RECEIVE_AND_SEE_SHARE], 'projects.avatar.store', 403 ],
            [ [Capability::RECEIVE_AND_SEE_SHARE], 'projects.avatar.destroy', 403 ],
            [ Capability::$PARTNER, 'projects.avatar.index', 200 ],
            [ [Capability::RECEIVE_AND_SEE_SHARE], 'projects.avatar.index', 200 ],
        ];
    }
    
    private function getFileForUpload()
    {
        $original_file = base_path('tests/data/project-avatar.png');
        $copy_file =  storage_path('app/project-avatar-testing.png');
        copy($original_file, $copy_file);

        $file = new UploadedFile(
            $copy_file,
            basename($copy_file),
            'image/png',
            filesize($copy_file),
            null,
            true
        );

        return $file;
    }

    private function createUser($capabilities, $userParams = [])
    {
        return tap(factory(User::class)->create($userParams))->addCapabilities($capabilities);
    }
     
    /**
     * Test the expected routes are available
     *
     * @dataProvider expected_routes_provider
     * @return void
     */
    public function testProjectAvatarRoutesExistence($route_name, $parameters)
    {
        // you will see InvalidArgumentException if the route is not defined
        
        route($route_name, $parameters);

        $this->assertTrue(true, "Test complete without exceptions");
    }
    
    /**
     * Test if some routes browsed after login are viewable or not and shows
     * the expected page and error code
     *
     * @dataProvider routes_and_capabilities_provider
     * @return void
     */
    public function testProjectAvatarIndex($caps, $route, $expected_return_code)
    {
        $params = null;
        
        $user = $this->createUser($caps);
        
        $project = factory(Project::class)->create([
            'avatar' => base_path('tests/data/project-avatar.png')
        ]);

        $method = Str::endsWith($route, 'index') ? 'get' : (Str::endsWith($route, 'store') ? 'post' : 'delete');

        $params = ['id' => $project->id ];

        if (! Str::endsWith($route, 'index')) {
            $params['_token'] = csrf_token();
        }

        $content = [];

        if (Str::endsWith($route, 'store')) {
            $content = ['avatar' => $this->getFileForUpload()];
        }

        $response = $this->actingAs($user)->{$method}(route($route, $params), $content);
            
        if ($expected_return_code !== 302 && $expected_return_code !== 200) {
            if ($response->isView()) {
                $response->assertErrorView($expected_return_code);
            } else {
                $response->assertStatus($expected_return_code);
            }
        } else {
            $response->assertStatus($expected_return_code);
        }
    }
    
    public function testProjectAvatarStore()
    {
        $project = factory(Project::class)->create();
        
        ;

        $params = [
            'id' => $project->id,
            '_token' => csrf_token()
        ];

        $file = $this->getFileForUpload();

        $response = $this->actingAs($project->manager)->json(
            'POST',
            route('projects.avatar.store', $params),
            ['avatar' => $file]
        );

        $response->assertJson(['status' => 'ok']);

        $project = $project->fresh();

        $this->assertNotNull($project->avatar);
    }
    
    public function testProjectAvatarStoreForbidden()
    {
        $project = factory(Project::class)->create();
    
        $params = [
            'id' => $project->id,
            '_token' => csrf_token()
        ];

        $file = $this->getFileForUpload();

        $response = $this->actingAs($this->createUser(Capability::$ADMIN))->json(
            'POST',
            route('projects.avatar.store', $params),
            ['avatar' => $file]
        );

        $response->assertJson(['status' => 'error']);
    }

    public function testProjectAvatarDelete()
    {
        copy(base_path('tests/data/project-avatar.png'), storage_path('app/projects/avatars/project-avatar.png'));

        $project = factory(Project::class)->create([
            'avatar' => storage_path('app/projects/avatars/project-avatar.png')
        ]);

        $params = [
            'id' => $project->id,
            '_token' => csrf_token()
        ];

        $response = $this->actingAs($project->manager)->delete(route('projects.avatar.destroy', $params));

        $response->assertJson(['status' => 'ok']);

        $project = $project->fresh();

        $this->assertNull($project->avatar);
    }

    public function testProjectAvatarDeleteForbidden()
    {
        $project = factory(Project::class)->create([
            'avatar' => storage_path('app/projects/avatars/project-avatar.png')
        ]);

        $params = [
            'id' => $project->id,
            '_token' => csrf_token()
        ];

        $response = $this->actingAs($this->createUser(Capability::$ADMIN))->delete(route('projects.avatar.destroy', $params));

        $response->assertJson(['status' => 'error']);
    }
}
