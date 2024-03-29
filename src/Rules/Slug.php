<?php

namespace Dimimo\PoolForum\Rules;

use Illuminate\Contracts\Validation\Rule;

class Slug implements Rule
{
    protected $pattern = '/^[a-z][-a-z0-9]*$/';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return empty($value) ||
            (is_string($value) && preg_match($this->pattern, $value) === 1);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute can have letters (A-Z), numbers (0-9) or dashes (-).';
    }
}
