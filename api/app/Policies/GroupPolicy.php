<?php

namespace App\Policies;

use App\Enums\UserGroupRoleEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Group $group): bool
    {
        return $user->role->isOwner() || $user->role->isAdmin();
    }

    public function update(User $user, Group $group): bool
    {
        if ($user->role->isOwner()) {
            return true;
        }
        if (!$user = $group->users()->find($user)) {
            return false;
        }
        if ($user->pivot->role_id->isOwner()) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Group $group): bool
    {
        if ($user->role->isOwner()) {
            return true;
        }
        if (!$user = $group->users()->find($user)) {
            return false;
        }
        if ($user->pivot->role_id->isOwner()) {
            return true;
        }

        return false;
    }

    public function userCreate(User $user, Group $group, User $targetUser): bool
    {
        if ($user->id === $targetUser->id) {
            return false;
        }
        if ($user->role->isOwner()) {
            return true;
        }
        if (!$user = $group->users()->find($user)) {
            return false;
        }
        if ($user->pivot->role_id->isOwner() || $user->pivot->role_id->isAdmin()) {
            return true;
        }

        return false;
    }

    public function userDelete(User $user, Group $group, User $targetUser): bool
    {
        if ($user->id === $targetUser->id) {
            return false;
        }

        $targetUserGroupRole = $group->getUserRole($targetUser);

        if ($targetUserGroupRole->isOwner()) {
            return false;
        }

        if ($user->role->isOwner()) {
            return true;
        }

        if (!$user = $group->users()->find($user)) {
            return false;
        }

        $userGroupRole = $group->getUserRole($user);

        if ($userGroupRole->isOwner()) {
            return true;
        }

        if ($userGroupRole->isAdmin() && $targetUserGroupRole->isCommon()) {
            return true;
        }

        return false;
    }

    public function userRoleUpdate(User $user, Group $group, User $targetUser, UserGroupRoleEnum $role): bool
    {
        if ($user->id === $targetUser->id) {
            return false;
        }

        $targetUserGroupRole = $group->getUserRole($targetUser);

        if ($targetUserGroupRole->isOwner()) {
            return false;
        }

        if ($user->role->isOwner()) {
            return true;
        }

        if (!$user = $group->users()->find($user)) {
            return false;
        }

        $userGroupRole = $group->getUserRole($user);

        if ($userGroupRole->isCommon() || $userGroupRole->isAdmin()) {
            return false;
        }

        return true;
    }
}
