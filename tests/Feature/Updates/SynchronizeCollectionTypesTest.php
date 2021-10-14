<?php

namespace Tests\Feature\Updates;

use KBox\Group;
use KBox\Jobs\Updates\SynchronizeCollectionTypes;
use Tests\TestCase;

class SynchronizeCollectionTypesTest extends TestCase
{
    public function test_collection_type_applied()
    {
        $personalCollection = factory(Group::class)->create([
            'is_private' => true,
            'type' => 0,
        ]);

        $projectCollection = factory(Group::class)->create([
            'is_private' => false,
            'type' => 0,
        ]);

        $job = app()->make(SynchronizeCollectionTypes::class);

        $job->handle();

        $this->assertEquals(Group::TYPE_PERSONAL, $personalCollection->fresh()->type);
        $this->assertEquals(Group::TYPE_PROJECT, $projectCollection->fresh()->type);
    }
}
