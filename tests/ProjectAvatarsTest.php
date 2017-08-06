<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KlinkDMS\Project;
use KlinkDMS\Capability;
use Illuminate\Http\UploadedFile;

class ProjectAvatarsTest extends BrowserKitTestCase
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
            [ Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH, 'projects.avatar.index', 200 ],
            [ Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH, 'projects.avatar.store', 403 ],
            [ Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH, 'projects.avatar.destroy', 403 ],
            [ Capability::$PROJECT_MANAGER, 'projects.avatar.index', 200 ],
            [ Capability::$PROJECT_MANAGER, 'projects.avatar.store', 403 ],
            [ Capability::$PROJECT_MANAGER, 'projects.avatar.destroy', 403 ],
            [ Capability::$DMS_MASTER, 'projects.avatar.index',  403 ],
            [ Capability::$DMS_MASTER, 'projects.avatar.store',  403 ],
            [ Capability::$DMS_MASTER, 'projects.avatar.destroy', 403 ],
            [ Capability::$PARTNER, 'projects.avatar.store', 403 ],
            [ Capability::$PARTNER, 'projects.avatar.destroy', 403 ],
            [ Capability::$GUEST, 'projects.avatar.store', 403 ],
            [ Capability::$GUEST, 'projects.avatar.destroy', 403 ],
            [ Capability::$PARTNER, 'projects.avatar.index', 200 ],
            [ Capability::$GUEST, 'projects.avatar.index', 200 ],
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
            true);

        return $file;
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
        
        $project = factory('KlinkDMS\Project')->create([
            'avatar' => base_path('tests/data/project-avatar.png')
        ]);

        
        $this->actingAs($user);

        \Session::start();
        
        $method = ends_with($route, 'index') ? 'get' : (ends_with($route, 'store') ? 'post' : 'delete');

        $params = ['id' => $project->id ];

        if (! ends_with($route, 'index')) {
            $params['_token'] = csrf_token();
        }

        $content = [];

        if (ends_with($route, 'store')) {
            $content = ['avatar' => $this->getFileForUpload()];
        }

        $this->{$method}(route($route, $params), $content);
            

        if ($expected_return_code !== 302 && $expected_return_code !== 200) {
            if (property_exists($this->response, 'original') && $this->response->original instanceof \Illuminate\View\View) {
                $this->assertViewName('errors.'.$expected_return_code);
            } else {
                $this->assertResponseStatus($expected_return_code);
            }
        } else {
            $this->assertResponseStatus($expected_return_code);
        }
    }
    
    public function testProjectAvatarStore()
    {
        $project = factory('KlinkDMS\Project')->create();
        \Session::start();
        
        $this->actingAs($project->manager);

        $params = [
            'id' => $project->id,
            '_token' => csrf_token()
        ];

        $file = $this->getFileForUpload();

        $this->call(
            'POST',
            route('projects.avatar.store', $params),
            [],
            [],
            ['avatar' => $file],
            ['HTTP_ACCEPT' => 'application/json']
        );

        $this->seeJson(['status' => 'ok']);

        $project = $project->fresh();

        $this->assertNotNull($project->avatar);
    }
    
    public function testProjectAvatarStoreForbidden()
    {
        $project = factory('KlinkDMS\Project')->create();
        \Session::start();
        
        $this->actingAs($this->createAdminUser());

        $params = [
            'id' => $project->id,
            '_token' => csrf_token()
        ];

        $file = $this->getFileForUpload();

        $this->call(
            'POST',
            route('projects.avatar.store', $params),
            [],
            [],
            ['avatar' => $file], ['HTTP_ACCEPT' => 'application/json']
        );

        $this->seeJson(['status' => 'error']);
    }

    public function testProjectAvatarDelete()
    {
        copy(base_path('tests/data/project-avatar.png'), storage_path('app/projects/avatars/project-avatar.png'));

        $project = factory('KlinkDMS\Project')->create([
            'avatar' => storage_path('app/projects/avatars/project-avatar.png')
        ]);
        \Session::start();
        
        $this->actingAs($project->manager);

        $params = [
            'id' => $project->id,
            '_token' => csrf_token()
        ];

        $this->delete(route('projects.avatar.destroy', $params));

        $this->seeJson(['status' => 'ok']);

        $project = $project->fresh();

        $this->assertNull($project->avatar);
    }

    public function testProjectAvatarDeleteForbidden()
    {
        $project = factory('KlinkDMS\Project')->create([
            'avatar' => storage_path('app/projects/avatars/project-avatar.png')
        ]);
        \Session::start();
        
        $this->actingAs($this->createAdminUser());

        $params = [
            'id' => $project->id,
            '_token' => csrf_token()
        ];

        $this->delete(route('projects.avatar.destroy', $params));

        $this->seeJson(['status' => 'error']);
    }
}
