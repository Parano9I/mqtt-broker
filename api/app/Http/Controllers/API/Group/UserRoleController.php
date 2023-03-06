<?php

namespace App\Http\Controllers\API\Group;

use App\Enums\UserGroupRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\User\RoleUpdateRequest;
use App\Models\Group;
use App\Models\User;
use App\Services\RoleService;
use Spatie\FlareClient\Http\Exceptions\NotFound;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRoleController extends Controller
{
    public function index(RoleService $service)
    {
        $roles = $service->getAllFormattedRoles(UserGroupRoleEnum::cases());

        return response()->json([
            'data' => [
                $roles
            ]
        ]);
    }

    public function update(RoleUpdateRequest $request, Group $group, User $user)
    {
        $request->validated();

        if (is_null($group->user($user))) {
            throw new NotFoundHttpException('User not found in group.');
        }

        $role = UserGroupRoleEnum::tryFrom($request->get('role_id'));

        $this->authorize('user-role-update', [$group, $user, $role]);

        $data = $group->users()->updateExistingPivot($user, ['role_id' => $role->value]);

        return response()->json('', 204);
    }
}
