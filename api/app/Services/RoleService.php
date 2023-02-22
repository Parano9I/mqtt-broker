<?php

namespace App\Services;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Enumerable;

class RoleService
{
    public function getAllFormattedRoles(array $roles): array
    {
        return collect($roles)->keys()->map(function ($id) use ($roles) {
            return [
                'id'         => $id,
                'type'       => 'role',
                'attributes' => [
                    'name' => $roles[$id]->getLabel()
                ]
            ];
        });
    }
}
