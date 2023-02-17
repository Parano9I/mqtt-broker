<?php

namespace App\Models;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    protected $table = 'groups_users';

    protected $casts = [
        'role' => UserGroupRoleEnum::class
    ];
}
