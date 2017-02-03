<?php
namespace KlinkDMS;

use Franzose\ClosureTable\Models\ClosureTable;

/**
 * Represents the closure table for the Groups (collections) to support
 * the hierarchy
 */
class GroupClosure extends ClosureTable implements GroupClosureInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'group_closure';
}
