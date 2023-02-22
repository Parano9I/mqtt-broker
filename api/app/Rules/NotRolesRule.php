<?php

namespace App\Rules;

use App\Enums\UserRoleEnum;
use Illuminate\Contracts\Validation\Rule;

class NotRolesRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !in_array($value, $this->roles);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
