<?php

namespace Tests\Feature\Http\Controllers\Group\User;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTest extends TestCase
{

    use RefreshDatabase;

    public function validation_invalid_casts()
    {
        return [
            'empty_user_id'        => [
                [
                    'user_id',
                    'role_id' => UserGroupRoleEnum::COMMON
                ]
            ],
            'user_id_type_string'  => [
                [
                    'user_id' => '9',
                    'role_id' => UserGroupRoleEnum::COMMON
                ]
            ],
            'user_id_type_decimal' => [
                [
                    'user_id' => 1.0,
                    'role_id' => UserGroupRoleEnum::COMMON
                ]
            ],
            'user_id_type_arr'     => [
                [
                    'user_id' => [],
                    'role_id' => UserGroupRoleEnum::COMMON
                ]
            ],
            'user_id_type_bool'    => [
                [
                    'user_id' => true,
                    'role_id' => UserGroupRoleEnum::COMMON
                ]
            ],
            'empty_role_id'        => [
                [
                    'user_id' => 0,
                    'role_id'
                ]
            ],
            'role_id_type_string'  => [
                [
                    'user_id' => 0,
                    'role_id' => '0'
                ]
            ],
            'role_id_type_decimal' => [
                [
                    'user_id' => 0,
                    'role_id' => 1.0
                ]
            ],
            'role_id_type_arr'     => [
                [
                    'user_id' => 0,
                    'role_id' => []
                ]
            ],
            'role_id_type_bool'    => [
                [
                    'user_id' => 0,
                    'role_id' => true
                ]
            ],
            'owner_role_is_banned' => [
                [
                    'user_id' => 0,
                    'role_id' => UserGroupRoleEnum::OWNER
                ]
            ],
            'role_is_not_exists'   => [
                [
                    'user_id' => 0,
                    'role_id' => 17
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
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $formInput);
        $response->assertStatus(422);
    }

    public function test_unauthorized()
    {
        $group = Group::factory()->create();

        $response = $this->postJson(route('api.groups.users.store', [$group->id]));
        $response->assertUnauthorized();
    }

    public function test_target_user_id_is_not_exists()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $data = [
            'user_id' => 3,
            'role_id' => UserGroupRoleEnum::COMMON
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'user_id' => Lang::get('validation.exists', ['attribute' => 'user id'])
            ]);
    }

    public function test_cannot_perform_actions_on_itself()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->hasAttached($user, ['role_id' => UserGroupRoleEnum::ADMIN])->create();

        $data = [
            'user_id' => $user->id,
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response->assertStatus(403);
    }

    public function test_target_user_must_not_be_in_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::ADMIN])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $data = [
            'user_id' => $targetUser->id,
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response
            ->assertStatus(409)
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'errors',
                    fn(AssertableJson $json) => $json
                        ->where('title', 'Conflict')
                        ->where('detail', 'Target user exists in group')
                        ->where('status', 409)

                )
            );
    }

    public function test_user_must_be_in_group_if_not_have_owner_role()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $data = [
            'user_id' => $targetUser->id,
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response->assertStatus(403);
    }

    public function test_owner_can_add_user_in_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $data = [
            'user_id' => $targetUser->id,
            'role_id' => UserGroupRoleEnum::COMMON
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response->assertStatus(204);
    }

    public function test_group_owner_can_add_user_in_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::OWNER])
            ->create();

        $data = [
            'user_id' => $targetUser->id,
            'role_id' => UserGroupRoleEnum::COMMON
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response->assertStatus(204);
    }

    public function test_group_admin_can_add_user_in_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::ADMIN])
            ->create();

        $data = [
            'user_id' => $targetUser->id,
            'role_id' => UserGroupRoleEnum::COMMON
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response->assertStatus(204);
    }

    public function test_group_user_with_common_role_cannot_add_user_in_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $data = [
            'user_id' => $targetUser->id,
            'role_id' => UserGroupRoleEnum::COMMON
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response->assertStatus(403);
    }
}
