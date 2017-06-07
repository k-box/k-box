<?php
namespace KlinkDMS;

use Franzose\ClosureTable\Models\ClosureTable;

/**
 * Represents the closure table for the Groups (collections) to support
 * the hierarchy
 *
 * @property int $closure_id
 * @property int $ancestor
 * @property int $descendant
 * @property int $depth
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\GroupClosure whereAncestor($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\GroupClosure whereClosureId($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\GroupClosure whereDepth($value)
 * @method static \Illuminate\Database\Query\Builder|\KlinkDMS\GroupClosure whereDescendant($value)
 * @mixin \Eloquent
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
