<?php

namespace Tests\Feature\Http\Controllers\User\Role;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{

    use RefreshDatabase;

    public function test_unauthorized()
    {
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);

        $response = $this->patchJson(route('api.users.roles.update', [$targetUser->id]));
        $response->assertUnauthorized();
    }

    public function test_empty_data()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);

        $data = ['role' => ''];

        $response = $this->patchJson(route('api.users.roles.update', [$targetUser->id]), $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'role' => Lang::get('validation.required', ['attribute' => 'role']),
            ]);
    }

    public function test_check_role_exists()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);

        $data = [
            'role' => 'super-admin'
        ];

        $response = $this->patchJson(route('api.users.roles.update', [$targetUser->id]), $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'role' => 'Such a role does not exist'
            ]);
    }

    public function test_target_owner_role_ban()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);

        $data = [
            'role' => UserRoleEnum::OWNER
        ];

        $response = $this->patchJson(route('api.users.roles.update', [$targetUser->id]), $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'role' => 'Cannot be assigned as an owner.'
            ]);
    }

    public function test_cannot_perform_actions_on_itself()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $data = [
            'role' => UserRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.users.roles.update', [$user->id]), $data);
        $response->assertStatus(403);
    }

    public function test_owner_can_update_role()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);

        $data = [
            'role' => UserRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.users.roles.update', [$targetUser->id]), $data);
        $response->assertNoContent();
    }

    public function test_admin_cannot_update_role()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);

        $data = [
            'role' => UserRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.users.roles.update', [$targetUser->id]), $data);
        $response->assertStatus(403);
    }

    public function test_common_user_cannot_update_role()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);
        $targetUser = User::factory()->create(['role' => UserRoleEnum::COMMON]);

        $data = [
            'role' => UserRoleEnum::ADMIN
        ];

        $response = $this->patchJson(route('api.users.roles.update', [$targetUser->id]), $data);
        $response->assertStatus(403);
    }
}
