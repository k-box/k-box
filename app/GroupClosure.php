<?php
namespace KlinkDMS;

use Franzose\ClosureTable\Models\ClosureTable;

class GroupClosure extends ClosureTable implements GroupClosureInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'group_closure';
}
