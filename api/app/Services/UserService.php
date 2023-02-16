<?php

namespace App\Services;

use App\Enums\UserRoleEnum;
use App\Models\User;

class UserService
{
    public function getDefaultRole(){
        return User::query()->count() ? UserRoleEnum::COMMON : UserRoleEnum::OWNER;
    }
}
