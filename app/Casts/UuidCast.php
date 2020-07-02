<?php

namespace KBox\Casts;

use Dyrynda\Database\Casts\EfficientUuid;

class UuidCast extends EfficientUuid
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        if (! $value) {
            return null;
        }

        return parent::get($model, $key, $value, $attributes);
    }
}
