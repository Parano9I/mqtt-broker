<?php

namespace App\Models;

use App\Enums\UserGroupRoleEnum;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{
    protected $casts = [
        'role_id' => UserGroupRoleEnum::class
    ];
}
