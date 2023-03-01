<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{

    use RefreshDatabase;

    public function test_on_empty_fields()
    {
        $user = [
            'login' => '',
            'email' => ''
        ];

        $response = $this->postJson(route('api.users.update'), $user);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'login' => Lang::get('validation.required', ['attribute' => 'login']),
                'email' => Lang::get('validation.required', ['attribute' => 'email']),
            ]);
    }

    public function test_unauthorized()
    {
        $response = $this->getJson(route('api.users.update'));
        $response->assertUnauthorized();
    }

    public function test_success_update()
    {
        $user = User::factory()->create([
            'login' => 'Parano1a',
            'email' => 'parano1a@gmail.com',
            'role'  => UserRoleEnum::COMMON
        ]);
        Sanctum::actingAs($user);

        $updateData = [
            'login' => 'Parano1a',
            'email' => 'test_mail@gmail.com'
        ];

        $response = $this->patchJson(route('api.users.update'), $updateData);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'user')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json->where('login', $updateData['login'])
                            ->where('email', $updateData['email'])
                            ->where('role', 'Common')
                        )
                )
            );
    }
}
