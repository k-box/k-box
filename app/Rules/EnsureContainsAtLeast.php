<?php

namespace KBox\Rules;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Validation\Rule;

/**
 * Check if an array contains at least the specified values
 */
class EnsureContainsAtLeast implements Rule
{
    private $requiredValues;

    private $customMessage = null;

    /**
     * Create a new rule instance.
     *
     * @param array $values The values that must be present
     * @return void
     */
    public function __construct(array $values)
    {
        $this->requiredValues = $values;
    }

    /**
     * Determine if the attribute is an
     * array that contains at least the specified values.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $common = count(array_intersect(Arr::wrap($value), $this->requiredValues) ?? []);
        $required = count($this->requiredValues);

        return $common === $required;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->customMessage) {
            return $this->customMessage;
        }
        return trans('validation.ensure_contains_at_least', [
            'required' => implode(',', $this->requiredValues)
        ]);
    }

    /**
     * Set the custom message to be used
     * @param string $message
     * @return \KBox\Rules\EnsureContainsAtLeast
     */
    public function setCustomMessage($message)
    {
        $this->customMessage = $message;

        return $this;
    }
}
