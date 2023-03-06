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

    private User $user;
    private string $rawPassword = 'password';

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function validation_invalid_casts()
    {
        return [
            'empty_login' => [
                ['login' => '', 'password' => 'password'],
            ],
            'empty_password' => [
                ['login' => 'login', 'password' => ''],
            ],
            'login_int_type' => [
                ['login' => 0, 'password' => 'password'],
            ],
            'login_float_type' => [
                ['login' => 0.0, 'password' => 'password'],
            ],
            'login_arr_type' => [
                ['login' => [], 'password' => 'password'],
            ],
            'login_not_exists' => [
                ['login' => 'login', 'password' => 'password'],
            ],
            'password_int_type' => [
                ['login' => 'Parano1a', 'password' => 0],
            ],
            'password_float_type' => [
                ['login' => 'Parano1a', 'password' => 0.0],
            ],
            'password_arr_type' => [
                ['login' => 'Parano1a', 'password' => []],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validation_invalid_casts
     */
    public function test_cannot_store_with_invalid_data($formInput)
    {
        $response = $this->postJson(route('api.auth.login'), $formInput);
        $response->assertStatus(422);
    }

    public function test_success()
    {
        $credentials = [
            'login'    => $this->user->login,
            'password' => $this->rawPassword
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
        $credentials = [
            'login'    => $this->user->login,
            'password' => 'wrongPassword'
        ];

        $response = $this->postJson(route('api.auth.login'), $credentials);
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
}
