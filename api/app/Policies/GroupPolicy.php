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
        if ($user->role->isOwner()) return true;
        if (!$user = $group->users()->find($user)) return false;
        if ($user->pivot->role_id->isOwner() || $user->pivot->role_id->isAdmin()) return true;

        return false;
    }

    public function delete(User $user, Group $group): bool
    {
        if ($user->role->isOwner()) return true;
        if (!$user = $group->users()->find($user)) return false;
        if ($user->pivot->role_id->isOwner()) return true;

        return false;
    }

    public function userCreate(User $user, Group $group): bool
    {
        if ($user->role->isOwner()) return true;
        if (!$user = $group->users()->find($user)) return false;
        if ($user->pivot->role_id->isOwner() || $user->pivot->role_id->isAdmin()) return true;

        return false;
    }

    public function userDelete(User $subjectUser, Group $group, User $objectUser): bool
    {
        if ($subjectUser->id === $objectUser->id) return false;
        if ($objectUserRoleInGroup = $group->getUserRole($objectUser)->isOwner()) return false;
        if ($subjectUser->role->isOwner()) return true;
        if ($subjectUserRoleInGroup = $group->getUserRole($subjectUser)->isCommon()) return false;
        if ($subjectUserRoleInGroup->value <= $objectUserRoleInGroup->value) return false;

        return true;
    }

    public function userRoleUpdate(User $subjectUser, Group $group, User $objectUser, UserGroupRoleEnum $role): bool
    {
        if ($subjectUser->id === $objectUser->id) return false;
        if ($objectUserRoleInGroup = $group->getUserRole($objectUser)->isOwner()) return false;
        if ($subjectUser->role->isOwner()) return true;
        if ($subjectUserRoleInGroup = $group->getUserRole($subjectUser)->isCommon()) return false;
        if ($subjectUserRoleInGroup->value <= $objectUserRoleInGroup->value) return false;

        return true;
    }
}
