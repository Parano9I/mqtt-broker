<?php

namespace Tests\Feature\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase
{

    use RefreshDatabase;
    public function test_on_empty_fields()
    {
        $credentials = [
            'login'    => '',
            'password' => '',
        ];

        $response = $this->postJson(route('api.auth.login'), $credentials);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'login'    => Lang::get('validation.required', ['attribute' => 'login']),
                'password' => Lang::get('validation.required', ['attribute' => 'password'])
            ]);
    }

    public function test_success()
    {
        $user = User::factory()->create([
            'login'    => 'Parano1a',
            'email'    => 'parano1a@gmail.com',
        ]);

        $credentials = [
            'login'    => 'Parano1a',
            'password' => 'password'
        ];

        $response = $this->postJson(route('api.auth.login'), $credentials);
        $response
            ->assertStatus(201)
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'token')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json->where('type', 'Bearer')
                            ->whereType('payload', 'string')
                        )
                )
            );
    }

    public function test_wrong_password()
    {
        $user = User::factory()->create([
            'login'    => 'Parano1a',
            'email'    => 'parano1a@gmail.com',
        ]);

        $userCredentials = [
            'login'    => 'Parano1a',
            'password' => 'passwordTest'
        ];

        $response = $this->postJson(route('api.auth.login'), $userCredentials);
        $response
            ->assertStatus(401)
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'errors',
                    fn(AssertableJson $json) => $json->where('title', 'Bad credentials')
                        ->where('detail', 'Wrong password')
                        ->where('status', 401)
                )
            );
    }

    public function test_user_not_exists()
    {
        $user = User::factory()->create([
            'login'    => 'Parano1a',
            'email'    => 'parano1a@gmail.com',
        ]);

        $userCredentials = [
            'login'    => 'wrongLogin',
            'password' => 'password'
        ];

        $response = $this->postJson(route('api.auth.login'), $userCredentials);
        $response
            ->assertStatus(422)->assertJsonValidationErrors([
                'login' => Lang::get('validation.exists', ['attribute' => 'login'])
            ]);
    }
}
