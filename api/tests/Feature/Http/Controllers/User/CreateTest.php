<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_success_create_first_user_with_owner_role()
    {
        $user = [
            'login'                 => 'Parano1a',
            'email'                 => 'parano1a@gmail.com',
            'password'              => 'parano1a',
            'password_confirmation' => 'parano1a'
        ];

        $response = $this->postJson(route('api.users.store'), $user);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'user')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json->where('login', $user['login'])
                            ->where('email', $user['email'])
                            ->where('role', 'Owner')
                        )
                )
            );
    }

    public function test_success_create_second_users_with_common_role()
    {
        $firstOwner = User::factory()->create();

        $user = [
            'login'                 => 'Parano1a',
            'email'                 => 'parano1a@gmail.com',
            'password'              => 'parano1a',
            'password_confirmation' => 'parano1a'
        ];

        $response = $this->postJson(route('api.users.store'), $user);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'user')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json->where('login', $user['login'])
                            ->where('email', $user['email'])
                            ->where('role', 'Common')
                        )
                )
            );
    }

    public function test_on_empty_fields()
    {
        $user = [
            'login'                 => '',
            'email'                 => '',
            'password'              => '',
            'password_confirmation' => ''
        ];

        $response = $this->postJson(route('api.users.store'), $user);
        $response
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors([
                'login'    => Lang::get('validation.required', ['attribute' => 'login']),
                'email'    => Lang::get('validation.required', ['attribute' => 'email']),
                'password' => Lang::get('validation.required', ['attribute' => 'password'])
            ]);
    }

    public function test_on_unique_login_and_email_fields()
    {
        $user = [
            'login'                 => 'Parano1a',
            'email'                 => 'parano1a@gmail.com',
            'password'              => 'parano1a',
            'password_confirmation' => 'parano1a'
        ];

        $firstOwner = User::factory()->create([
            'login' => 'Parano1a',
            'email' => 'parano1a@gmail.com'
        ]);

        $response = $this->postJson(route('api.users.store'), $user);
        $response
            ->assertStatus(422)
            ->assertJsonMissingValidationErrors([
                'login' => Lang::get('validation.unique', ['attribute' => 'login']),
                'email' => Lang::get('validation.unique', ['attribute' => 'email']),
            ]);
    }
}
