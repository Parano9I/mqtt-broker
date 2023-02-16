<?php

namespace App\Http\Controllers\API\User;

use App\Enums\UserRoleEnum;
use App\Exceptions\PermissionDeniedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Rules\RoleRule;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function index()
    {
        $rolesArray = UserRoleEnum::cases();
        $roles = collect($rolesArray)->keys()->map(function ($id) use($rolesArray) {
            return [
                'id' => $id,
                'type' => 'role',
                'attributes' => [
                    'name' => $rolesArray[$id]->getLabel()
                ]
            ];
        });

        return response([
            'data' => [
                $roles
            ]
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update-role', $user);

        $request->validate([
            'role' => ['required', 'string', new RoleRule()]
        ]);
        $role = UserRoleEnum::tryFrom($request->get('role'));

        if($role->isOwner()){
            throw new PermissionDeniedException('There can only be one owner');
        }

        $subject = $request->user();

        if ($subject->id === $user->id) {
            return response([
                'errors' => [
                    'title' => 'Bad params',
                    'detail' => 'The user cannot change his role',
                    'status' => 401
                ]
            ], 401);
        }

        $user->update(['role' => $role]);

        return response([
            'data' => new UserResource($user)
        ]);
    }
}
