<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
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
    
    public function capabilities_provider()
    {
        return [
            [ Capability::$ADMIN],
            [ Capability::$PROJECT_MANAGER],
            [ [Capability::MANAGE_KBOX]],
            [ Capability::$PARTNER],
            [ [Capability::RECEIVE_AND_SEE_SHARE]],
        ];
    }

    public function forbidden_capabilities_provider()
    {
        return [
            [ Capability::$PROJECT_MANAGER],
            [ Capability::$PARTNER],
            [ [Capability::RECEIVE_AND_SEE_SHARE]],
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
     * @dataProvider capabilities_provider
     * @return void
     */
    public function testProjectAvatarIndex($caps)
    {
        $user = $this->createUser($caps);
        
        $project = factory(Project::class)->create([
            'avatar' => base_path('tests/data/project-avatar.png')
        ]);

        $project->users()->attach($user);

        $response = $this->actingAs($user)->get(route('projects.avatar.index', ['id' => $project->id ]));
            
        $response->assertOk();

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $response->baseResponse);
    }
    
    /**
     * @dataProvider forbidden_capabilities_provider
     */
    public function test_avatar_cannot_be_viewed_by_users_non_members($caps)
    {
        $user = $this->createUser($caps);
        
        $project = factory(Project::class)->create([
            'avatar' => base_path('tests/data/project-avatar.png')
        ]);

        $response = $this->actingAs($user)->get(route('projects.avatar.index', ['id' => $project->getKey()]));
            
        $response->assertForbidden();
    }
    
    public function testProjectAvatarStore()
    {
        $project = factory(Project::class)->create();

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
