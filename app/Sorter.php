<?php

namespace KBox;

use DmsRouting;
use Illuminate\Http\Request;

class Sorter
{
    public const ORDER_ASCENDING = 'a';
    
    public const ORDER_DESCENDING = 'd';

    public static $mapToOrder = [
        self::ORDER_ASCENDING => 'ASC',
        self::ORDER_DESCENDING => 'DESC',
    ];

    public $field;

    public $sortables = [];

    public $column = 'updated_at';
    
    public $direction;

    public $order = 'ASC';
    
    public $type = 'string';

    protected function getFields($entity = null)
    {
        if ($entity==='starred') {
            return Starred::sortableFields();
        } elseif ($entity==='shared') {
            return Shared::sortableFields();
        }
        
        return DocumentDescriptor::sortableFields();
    }

    public function ensureValidDirection($direction, $default = self::ORDER_ASCENDING)
    {
        if (is_null($direction) || ! in_array($direction, [self::ORDER_ASCENDING, self::ORDER_DESCENDING])) {
            return $default;
        }

        return $direction;
    }
    
    public function ensureValidField($field, $default = 'name')
    {
        if (is_null($field) || ! in_array($field, array_keys($this->sortables))) {
            return $default;
        }

        return $field;
    }

    /**
     * Create a sortable configuration from
     * the current request
     *
     * @param Illuminate\Http\Request $request
     */
    public static function fromRequest(Request $request, $entity = 'document', $defaultColumn = 'update_date', $defaultOrder = self::ORDER_DESCENDING)
    {
        // TODO: maybe the defaults can be configured on the reference entity?

        $instance = new self;

        $instance->sortables = $instance->getFields($entity);

        $instance->direction = $instance->ensureValidDirection(e($request->input('o')), $defaultOrder);
        $instance->order = self::$mapToOrder[$instance->direction] ?? 'ASC';

        $instance->field  = $instance->ensureValidField(e($request->input('sc')), $defaultColumn);

        list($field, $type) = $instance->sortables[$instance->field] ?? $instance->sortables[$defaultColumn];

        $instance->type = $type;
        $instance->column = $field;

        return $instance;
    }

    /**
     * Return if sorting is in descending order
     *
     * @return bool
     */
    public function isDesc()
    {
        return $this->direction === self::ORDER_DESCENDING;
    }

    /**
     * Return if sorting is in ascending order
     *
     * @return bool
     */
    public function isAsc()
    {
        return $this->direction === self::ORDER_ASCENDING;
    }

    /**
     * Check if a field is the current sorting field
     *
     * @param string $field The field to check
     * @return bool
     */
    public function current($field)
    {
        return $this->field === $field;
    }

    /**
     * Get the type of the sorting field
     *
     * @param string $key The sorting field you would like to know the type. Default null, the current type field will be returned
     * @return string
     */
    public function type($field = null)
    {
        if (is_null($field)) {
            return $this->type;
        }
        
        return $this->sortables[$field][1] ?? 'string';
    }

    /**
     * Check if a field is sortable
     *
     * @param string $field
     * @return bool
     */
    public function isSortable($field)
    {
        if (empty($field)) {
            return false;
        }
        
        return in_array($field, array_keys($this->sortables));
    }

    /**
     * Build the URL for the change in the sorting configuration.
     * Retrieves the currently set request parameters, if any
     *
     * @param array $params The associative array with the updated sorting options. Fields: `sc` or `o`
     * @return string
     */
    public function url($params = [])
    {
        return DmsRouting::safeCurrentUrl($params);
    }
}
