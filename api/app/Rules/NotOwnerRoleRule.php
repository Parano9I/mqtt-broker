<?php

namespace App\Rules;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Contracts\Validation\Rule;

class NotOwnerRoleRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value !== UserRoleEnum::OWNER->value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Cannot be assigned as an owner.';
    }
}
