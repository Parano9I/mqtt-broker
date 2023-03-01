<?php

namespace Tests\Feature\Http\Controllers\Group\User;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized()
    {
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        $group = Group::factory()->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertUnauthorized();
    }

    public function test_target_user_must_be_in_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
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

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(403);
    }

    public function test_cannot_perform_actions_on_itself()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->hasAttached($user, ['role_id' => UserGroupRoleEnum::ADMIN])->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $user->id]));
        $response->assertStatus(403);
    }

    public function test_cannot_delete_group_owner()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $group = Group::factory()->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::OWNER])->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(403);
    }

    public function test_owner_can_delete_group_admin()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::ADMIN])
            ->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(204);
    }

    public function test_owner_can_delete_group_common()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(204);
    }

    public function test_group_owner_can_delete_group_admin()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::OWNER])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::ADMIN])
            ->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(204);
    }

    public function test_group_owner_can_delete_group_common()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::OWNER])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(204);
    }

    public function test_group_admin_cannot_delete_group_admin()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::ADMIN])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::ADMIN])
            ->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(403);
    }

    public function test_group_admin_can_delete_group_common()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::ADMIN])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(204);
    }

    public function test_group_common_cannot_delete_group_admin()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::COMMON])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::ADMIN])
            ->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(403);
    }

    public function test_group_common_cannot_delete_group_common()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()
            ->hasAttached($user, ['role_id' => UserGroupRoleEnum::COMMON])
            ->hasAttached($targetUser, ['role_id' => UserGroupRoleEnum::COMMON])
            ->create();

        $response = $this->deleteJson(route('api.groups.users.destroy', [$group->id, $targetUser->id]));
        $response->assertStatus(403);
    }
}
