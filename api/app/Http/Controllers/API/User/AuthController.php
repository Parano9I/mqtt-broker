<?php

namespace App\Http\Controllers\API\User;

use App\DTOs\TokenDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Resources\TokenResource;
use App\Models\User;
use App\Services\Hasher;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request, Hasher $hasher)
    {
        $credentials = $request->validated();
        $user = User::query()->where('login', $credentials['login'])->firstOrFail();

        if (!$hasher->verify($user->password, $credentials['password'])) {
            return response([
                'errors' => [
                    'title' => 'Bad credentials',
                    'detail' => 'Wrong password',
                    'status' => 401
                ]
            ], 401);
        }

        $token = new TokenDTO;
        $token->userId = $user->id;
        $token->payload = $user->createToken('access_token')->plainTextToken;

        return response([
            new TokenResource($token)
        ], 201);

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response([
            'messages' => [
                'title' => 'Logged out',
                'detail' => 'Logged out is success',
                'status' => 200
            ]
        ]);
    }

}