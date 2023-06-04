<?php

namespace App\Http\Controllers\API\User;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Hasher;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(
        private UserService $userService
    ) {
    }

    public function index(Request $request) {
        $this->authorize('index', User::class);

        $user = $request->user();

        $users = User::query()
            ->whereNot('id', $user->id)
            ->when($user->role->isAdmin(), function (Builder $query) {
               return $query->where('role', UserRoleEnum::COMMON);
            })->get();

        return response()->json([
            'data' => UserResource::collection($users)
        ]);
    }

    public function store(StoreRequest $request, Hasher $hasher)
    {
        $data = $request->validated();

        $user = User::create([
            'login'    => $data['login'],
            'email'    => $data['email'],
            'password' => $hasher->hash($data['password']),
            'role'     => $this->userService->getDefaultRole()
        ]);

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $user = $request->user();
        $user->update($request->validated());

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    public function destroy(Request $request)
    {
        $this->authorize('delete', User::class);

        $user = $request->user();
        $user->deleteOrFail();

        return response()->json([
            'message' => [
                'title'  => "User deleted",
                'detail' => "User deleted from organization",
                'status' => 200
            ]
        ]);
    }
}
