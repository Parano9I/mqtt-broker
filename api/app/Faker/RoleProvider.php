<?php

namespace App\Faker;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\User;
use Faker\Provider\Base;

class RoleProvider extends Base
{
    public function role(): string
    {
        $roles = UserRoleEnum::cases();

        if (User::count()) {
            return $roles[rand(1, count($roles) - 1)]->value;
        } else {
            return UserRoleEnum::OWNER->value;
        }
    }

    public function groupRole(): int
    {
        $roles = UserGroupRoleEnum::cases();

        if (User::count()) {
            return $roles[rand(1, count($roles) - 1)]->value;
        } else {
            return UserGroupRoleEnum::OWNER->value;
        }
    }
}
