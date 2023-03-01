<?php

namespace App\Models;

use App\Enums\UserGroupRoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupUser extends Pivot
{

    use HasFactory;

    protected $table = 'groups_users';

    protected $casts = [
        'role_id' => UserGroupRoleEnum::class
    ];
}
