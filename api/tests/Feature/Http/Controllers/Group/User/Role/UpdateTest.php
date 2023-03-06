<?php

namespace Tests\Feature\Http\Controllers\Group\User\Role;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function validation_invalid_casts()
    {
        return [
            'empty_role_id'        => [
                [
                    'role_id'
                ]
            ],
            'role_id_type_string'  => [
                [
                    'role_id' => '0'
                ]
            ],
            'role_id_type_decimal' => [
                [
                    'role_id' => 1.2
                ]
            ],
            'role_id_type_arr'     => [
                [
                    'role_id' => []
                ]
            ],
            'owner_role_is_banned' => [
                [
                    'role_id' => UserGroupRoleEnum::OWNER
                ]
            ],
            'role_is_not_exists'   => [
                [
                    'role_id' => 17
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
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $response = $this->patchJson(route('api.groups.users.roles.update', [$group->id, $targetUser->id]), $formInput);
        $response->assertStatus(422);
    }


    public function test_unauthorized()
    {
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        $group = Group::factory()->create();

        $response = $this->patchJson(route('api.groups.users.roles.update', [$group->id, $targetUser->id]));
        $response->assertUnauthorized();
    }

    public function test_target_user_must_be_in_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $data = [
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.groups.users.roles.update', [$group->id, $targetUser->id]), $data);
        $response->assertNotFound();
    }

    public function test_user_must_be_in_group_if_not_owner()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $data = [
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.groups.users.roles.update', [$group->id, $targetUser->id]), $data);
        $response->assertStatus(403);
    }

    public function test_cannot_perform_actions_on_itself()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->hasAttached($user, ['role_id' => UserGroupRoleEnum::OWNER])->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $user->id]));
        $response->assertStatus(403);
    }

    public function test_cannot_update_role_if_target_user_is_owner()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::OWNER])
            ->create();

        $data = [
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.groups.users.roles.update', [$group->id, $targetUser->id]), $data);
        $response->assertStatus(403);
    }

    public function test_group_admin_cannot_update_role()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::COMMON])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $data = [
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.groups.users.roles.update', [$group->id, $targetUser->id]), $data);
        $response->assertStatus(403);
    }

    public function test_group_common_cannot_update_role()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::ADMIN])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $data = [
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.groups.users.roles.update', [$group->id, $targetUser->id]), $data);
        $response->assertStatus(403);
    }

    public function test_group_owner_can_update_role()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::OWNER])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $data = [
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.groups.users.roles.update', [$group->id, $targetUser->id]), $data);
        $response->assertNoContent();
    }

    public function test_owner_can_update_role()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $data = [
            'role_id' => UserGroupRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.groups.users.roles.update', [$group->id, $targetUser->id]), $data);
        $response->assertNoContent();
    }


}
