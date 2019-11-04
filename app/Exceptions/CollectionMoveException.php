<?php

namespace KBox\Exceptions;

use Exception;
use Illuminate\Support\Arr;
use KBox\Group;

/**
* When moving a collection was not possible
*/
final class CollectionMoveException extends Exception
{
    const REASON_NOT_ALL_SAME_USER = 0;

    private $collection = null;
    private $reason = null;
    private $causedBy = null;

    /**
     *
     * @param Group $collection the collection that cannot be moved
     * @param int $reason the reason why it was not possible to move
     * @param Group[] $causedBy the additional collections that caused the move to fail. Use this to explicitly tell what descendant caused the failure of the $collection move
     */
    public function __construct(Group $collection, $reason, $causedBy = [])
    {
        $this->collection = $collection;
        $this->reason = $reason;
        $this->causedBy = collect(Arr::wrap($causedBy))->flatten();
        $names = $this->causedBy->map(function ($c) {
            if (\is_a($c, Group::class)) {
                return $c->name;
            }
            return null;
        })->filter();

        parent::__construct(trans($this->causedBy->isEmpty() ? 'groups.move.errors.personal_not_all_same_user_empty_cause' : 'groups.move.errors.personal_not_all_same_user', [
            'collection' => $this->collection->name,
            'collection_cause' => $names->implode(', '),
        ]), 22);
    }

    public function collection()
    {
        return $this->collection;
    }

    public function reason()
    {
        return $this->reason;
    }

    public function causes()
    {
        return $this->causedBy;
    }
}
