<?php

namespace Tests\Feature\Http\Controllers\Group;

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
        $response = $this->deleteJson(route('api.groups.destroy', [1]));
        $response->assertUnauthorized();
    }

    public function test_not_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'test',
            'description' => 'test'
        ];

        $response = $this->deleteJson(route('api.groups.destroy', [24]), $data);
        $response->assertNotFound();
    }

    public function test_owner_can_delete_group()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $response = $this->deleteJson(route('api.groups.destroy', [$group->id]));
        $response->assertNoContent();
    }

    public function test_admin_cannot_delete_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $response = $this->deleteJson(route('api.groups.destroy', [$group->id]));
        $response->assertStatus(403);
    }

    public function test_common_cannot_delete_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $response = $this->deleteJson(route('api.groups.destroy', [$group->id]));
        $response->assertStatus(403);
    }

    public function test_group_owner_can_delete_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->hasAttached($user, ['role_id' => UserGroupRoleEnum::OWNER])->create();

        $response = $this->deleteJson(route('api.groups.destroy', [$group->id]));
        $response->assertNoContent();
    }

    public function test_group_admin_cannot_delete_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->hasAttached($user, ['role_id' => UserGroupRoleEnum::ADMIN])->create();

        $response = $this->deleteJson(route('api.groups.destroy', [$group->id]));
        $response->assertStatus(403);
    }

    public function test_group_common_cannot_delete_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $group = Group::factory()->hasAttached($user, ['role_id' => UserGroupRoleEnum::COMMON])->create();

        $response = $this->deleteJson(route('api.groups.destroy', [$group->id]));
        $response->assertStatus(403);
    }
}
