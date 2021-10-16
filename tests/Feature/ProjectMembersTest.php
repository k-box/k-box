<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Project;
use Tests\TestCase;
use KBox\Capability;
use Tests\Concerns\ClearDatabase;

use Illuminate\Support\Facades\Event;
use KBox\Events\ProjectMembersAdded;
use KBox\Events\ProjectMembersRemoved;

class ProjectMembersTest extends TestCase
{
    use ClearDatabase;

    private function createUser($capabilities, $userParams = [])
    {
        return tap(User::factory()->create($userParams))->addCapabilities($capabilities);
    }
    
    public function test_members_can_be_added()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $members = collect([
            $this->createUser(Capability::$PARTNER),
            $this->createUser(Capability::$PARTNER),
        ]);

        $params = [
            'manager' => $user->id,
            'name' => 'Project title',
            'description' => null,
            'users' => $members->pluck('id')->toArray(),
        ];

        Event::fake();

        $response = $this->from(route('projects.create'))
            ->actingAs($user)
            ->post(route('projects.store'), $params);

        $created_project = Project::where('name', 'Project title')->first();

        $current_members = $created_project->users()->orderBy('id', 'asc')->get();

        $this->assertCount(2, $current_members);

        $this->assertEquals($members->pluck('id')->toArray(), $current_members->pluck('id')->toArray());

        Event::assertDispatched(ProjectMembersAdded::class, function ($e) use ($created_project, $current_members) {
            return $e->project->is($created_project)
                && collect($e->members)->diff($current_members->pluck('id'))->isEmpty();
        });
    }

    public function test_members_can_be_removed()
    {
        $user = $this->createUser(Capability::$PROJECT_MANAGER);

        $members = collect([
            $this->createUser(Capability::$PARTNER),
            $this->createUser(Capability::$PARTNER),
        ]);
        
        $project = factory(Project::class)->create([
            'user_id' => $user->getKey()
        ]);

        $project->users()->toggle($members->pluck('id')->toArray());

        $params = [
            'manager' => $user->getKey(),
            'name' => $project->name,
            // 'description' => null,
            'users' => $members->pluck('id')->toArray(),
        ];

        Event::fake();

        $response = $this->from(route('projects.show', $project->getKey()))
            ->actingAs($user)
            ->put(route('projects.update', $project->getKey()), $params);

        $response->assertSessionDoesntHaveErrors();

        $updated_project = $project->fresh();

        $current_members = $updated_project->users()->orderBy('id', 'asc')->get();

        $this->assertCount(0, $current_members);

        Event::assertDispatched(ProjectMembersRemoved::class, function ($e) use ($updated_project, $members) {
            return $e->project->is($updated_project)
                && collect($e->members)->diff($members->pluck('id'))->isEmpty();
        });
    }
}
