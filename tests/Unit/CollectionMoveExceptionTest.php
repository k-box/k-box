<?php

namespace Tests\Unit;

use Tests\TestCase;
use KBox\Exceptions\CollectionMoveException;

class CollectionMoveExceptionTest extends TestCase
{
    public function test_current_collection_is_reported()
    {
        $collection = factory('KBox\Group')->make(['user_id' => 10]);

        $exception = new CollectionMoveException($collection, CollectionMoveException::REASON_NOT_ALL_SAME_USER);

        $this->assertTrue($exception->collection()->is($collection));
        $this->assertEquals(CollectionMoveException::REASON_NOT_ALL_SAME_USER, $exception->reason());
        $this->assertEquals(trans('groups.move.errors.personal_not_all_same_user_empty_cause', ['collection' => $collection->name]), $exception->getMessage());
    }

    public function test_cause_collection_is_reported()
    {
        $collection = factory('KBox\Group')->make(['user_id' => 10]);
        $cause = factory('KBox\Group')->make(['user_id' => 11]);

        $exception = new CollectionMoveException($collection, CollectionMoveException::REASON_NOT_ALL_SAME_USER, $cause);

        $this->assertTrue($exception->collection()->is($collection));
        $this->assertEquals(1, $exception->causes()->count());
        $this->assertTrue($exception->causes()->first()->is($cause));
        $this->assertEquals(trans('groups.move.errors.personal_not_all_same_user', [
            'collection' => $collection->name,
            'collection_cause' => $cause->name,
        ]), $exception->getMessage());
    }

    public function test_multiple_cause_collection_is_reported()
    {
        $collection = factory('KBox\Group')->make(['user_id' => 10]);
        $causes = factory('KBox\Group', 3)->make(['user_id' => 11]);

        $exception = new CollectionMoveException($collection, CollectionMoveException::REASON_NOT_ALL_SAME_USER, $causes);

        $this->assertTrue($exception->collection()->is($collection));
        $this->assertEquals(3, $exception->causes()->count());
        $this->assertEquals($causes->toArray(), $exception->causes()->toArray());
        $this->assertEquals(trans('groups.move.errors.personal_not_all_same_user', [
            'collection' => $collection->name,
            'collection_cause' => $causes->pluck('name')->values()->implode(', '),
        ]), $exception->getMessage());
    }
}
