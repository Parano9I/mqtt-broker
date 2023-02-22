<?php

namespace App\Http\Controllers\API\User;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Role\StoreRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\RoleService;

class RoleController extends Controller
{

    public function index(RoleService $service)
    {
        $roles  = $service->getAllFormattedRoles(UserRoleEnum::cases());

        return response()->json([
            'data' => [
                $roles
            ]
        ]);
    }

    public function update(StoreRequest $request, User $user)
    {
        $this->authorize('update-role', $user);

        $request->validated();
        $role = UserRoleEnum::tryFrom($request->get('role'));
        $user->update(['role' => $role]);

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }
}
