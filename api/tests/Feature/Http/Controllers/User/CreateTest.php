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

    public function validation_invalid_casts()
    {
        return [
            'empty_login'                         => [
                [
                    'login'                 => '',
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => 'password'
                ]
            ],
            'login_int_type'                      => [
                [
                    'login'                 => 0,
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => 'password'
                ]
            ],
            'login_float_type'                    => [
                [
                    'login'                 => 0.0,
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => 'password'
                ]
            ],
            'login_arr_type'                      => [
                [
                    'login'                 => [],
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => 'password'
                ]
            ],
            'empty_email'                         => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => '',
                    'password'              => 'password',
                    'password_confirmation' => 'password'
                ]
            ],
            'email_int_type'                      => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 0,
                    'password'              => 'password',
                    'password_confirmation' => 'password'
                ]
            ],
            'email_float_type'                    => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 0.0,
                    'password'              => 'password',
                    'password_confirmation' => 'password'
                ]
            ],
            'email_arr_type'                      => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => [],
                    'password'              => 'password',
                    'password_confirmation' => 'password'
                ]
            ],
            'empty_confirm_password'              => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => ''
                ]
            ],
            'empty_password'                      => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 'email@gmail.com',
                    'password'              => '',
                    'password_confirmation' => 'password'
                ]
            ],
            'password_int_type'                   => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 'email@gmail.com',
                    'password'              => 0,
                    'password_confirmation' => 'password'
                ]
            ],
            'password_float_type'                 => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 'email@gmail.com',
                    'password'              => 0.0,
                    'password_confirmation' => 'password'
                ]
            ],
            'password_arr_type'                   => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 'email@gmail.com',
                    'password'              => [],
                    'password_confirmation' => 'password'
                ]
            ],
            'password_confirm_int_type'           => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => 0
                ]
            ],
            'password_confirm_float_type'         => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => 0.0
                ]
            ],
            'password_confirm_arr_type'           => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => []
                ]
            ],
            'password_confirm_not_equal_password' => [
                [
                    'login'                 => 'Parano1a',
                    'email'                 => 'email@gmail.com',
                    'password'              => 'password',
                    'password_confirmation' => 'pass'
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider validation_invalid_casts
     */
    public function test_cannot_store_with_invalid_data($formInput)
    {
        $response = $this->postJson(route('api.users.store'), $formInput);
        $response->assertStatus(422);
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
            ->assertJsonValidationErrors([
                'login' => Lang::get('validation.unique', ['attribute' => 'login']),
                'email' => Lang::get('validation.unique', ['attribute' => 'email']),
            ]);
    }
}
