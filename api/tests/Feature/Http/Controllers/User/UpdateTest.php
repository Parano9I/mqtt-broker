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

    public function validation_invalid_casts()
    {
        return [
            'empty_login'      => [
                [
                    'login'    => '',
                    'password' => 'password',
                ]
            ],
            'login_int_type'   => [
                [
                    'login'    => 0,
                    'password' => 'password',
                ]
            ],
            'login_float_type' => [
                [
                    'login'    => 0.0,
                    'password' => 'password',
                ]
            ],
            'login_arr_type'   => [
                [
                    'login'    => [],
                    'password' => 'password',
                ]
            ],
            'empty_email'      => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => '',
                ]
            ],
            'email_int_type'   => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 0,
                ]
            ],
            'email_float_type' => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 0.0,
                ]
            ],
            'email_arr_type'   => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => [],
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider validation_invalid_casts
     */
    public function test_cannot_update_with_invalid_data($formInput)
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('api.users.update'), $formInput);
        $response->assertStatus(422);
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
