<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function delete(User $user)
    {
        if ($user->role->isOwner()) {
            return false;
        }

        return true;
    }

    public function updateRole(User $user, User $targetUser): bool
    {
        if ($user->id === $targetUser->id) {
            return false;
        }

        if ($user->role->isOwner()) {
            return true;
        }

        return false;
    }
}
