<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function delete(User $user){
        if($user->role->isOwner()) return false;

        return true;
    }

    public function updateRole(User $user, User $objectUser): bool
    {
        if(!$user->role->isOwner()) return false;
        if($user->id === $objectUser->id) return false;

        return true;
    }
}
