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

    public function test_unauthorized()
    {
        $group = Group::factory()->create();

        $response = $this->postJson(route('api.groups.users.store', [$group->id]));
        $response->assertUnauthorized();
    }

    public function test_empty_post_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $data = [
            'user_id' => '',
            'role_id' => ''
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'user_id' => Lang::get('validation.required', ['attribute' => 'user id']),
                'role_id' => Lang::get('validation.required', ['attribute' => 'role id']),
            ]);
    }

    public function test_target_user_id_is_not_exists(){
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

    public function test_target_role_id_is_not_exists(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $data = [
            'user_id' => $user->id,
            'role_id' => 12
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'role_id' => 'Such a role does not exist'
            ]);
    }

    public function test_target_owner_role_ban(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $data = [
            'user_id' => $user->id,
            'role_id' => UserGroupRoleEnum::OWNER
        ];

        $response = $this->postJson(route('api.groups.users.store', [$group->id]), $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'role_id' => 'Cannot be assigned as an owner.'
            ]);
    }

    public function test_cannot_perform_actions_on_itself(){
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

    public function test_target_user_must_not_be_in_group(){
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

    public function test_user_must_be_in_group_if_not_have_owner_role(){
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

    public function test_owner_can_add_user_in_group(){
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

    public function test_group_owner_can_add_user_in_group(){
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

    public function test_group_admin_can_add_user_in_group(){
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

    public function test_group_user_with_common_role_cannot_add_user_in_group(){
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
