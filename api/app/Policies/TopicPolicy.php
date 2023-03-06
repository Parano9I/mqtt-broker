<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Group $group)
    {
        if ($user->role->isOwner()) {
            return true;
        }

        $userGroupRole = $group->getUserRole($user);

        if (empty($userGroupRole)) {
            return false;
        }

        if ($userGroupRole->isOwner() || $userGroupRole->isAdmin()) {
            return true;
        }

        return false;
    }

    public function update(User $user, Group $group)
    {
        if ($user->role->isOwner()) {
            return true;
        }

        $userGroupRole = $group->getUserRole($user);

        if (empty($userGroupRole)) {
            return false;
        }

        if ($userGroupRole->isOwner() || $userGroupRole->isAdmin()) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Group $group)
    {
        if ($user->role->isOwner()) {
            return true;
        }

        $userGroupRole = $group->getUserRole($user);

        if (empty($userGroupRole)) {
            return false;
        }

        if ($userGroupRole->isOwner() || $userGroupRole->isAdmin()) {
            return true;
        }

        return false;
    }
}
