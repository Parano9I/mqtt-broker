<?php

namespace App\Http\Controllers\API\User;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Hasher;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(
        private UserService $userService
    )
    {
    }

    public function index()
    {
        return response()->json([
            'data' => UserResource::collection(User::paginate(10))
        ]);
    }

    public function store(StoreRequest $request, Hasher $hasher)
    {
        $data = $request->validated();

        $user = User::create([
            'login' => $data['login'],
            'email' => $data['email'],
            'password' => $hasher->hash($data['password']),
            'role' => $this->userService->getDefaultRole()
        ]);

        return response()->json([
            'data' => [
                new UserResource($user)
            ]
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $user = $request->user()->update($request->validated());

        return response()->json([
            'data' => [
                new UserResource($user)
            ]
        ]);
    }

    public
    function destroy(Request $request, $id)
    {
        $user = $request->user()->update($request->validated());
        $user->deleteOrFail();

        return response()->json([
            'messages' => [
                'title' => "User with $id id deleted",
                'detail' => "User with $id id deleted",
                'status' => 200
            ]
        ]);
    }
}
