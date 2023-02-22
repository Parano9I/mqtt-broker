<?php

namespace App\Http\Controllers\API\Group;

use App\Enums\UserGroupRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\User\StoreRequest;
use App\Http\Resources\Group\UserResource;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class UserController extends Controller
{
    public function index(Request $request, Group $group)
    {
        $users = $group->users()->paginate(10);

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }

    public function store(StoreRequest $request, Group $group)
    {
        $this->authorize('user-create', $group);

        $data = $request->validated();
        $user = User::findOrFail($data['userId']);

        if (!is_null($group->users()->find($user))) return response()->json([
            'errors' => [
                'title' => 'Conflict',
                'detail' => 'The user is already in the group',
                'status' => 409
            ]
        ], 409);

        $group->users()->attach($user, ['role_id' => $data['role']]);

        return response()->json('', 204);
    }

    public function destroy(Group $group, User $user)
    {
        if(is_null($group->user($user))) {
            throw new NotFound('User not found in group.');
        }

        $this->authorize('user-delete', [$group, $user]);

        $group->users()->detach($user);

        return response()->json('', 204);
    }
}
