<?php

namespace Tests\Feature\Http\Controllers\Organization;

use App\Enums\UserRoleEnum;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTest extends TestCase
{

    use RefreshDatabase;

    public function test_unauthorized()
    {
        $organization = Organization::factory()->create();

        $response = $this->deleteJson(
            route('api.organizations.destroy', [$organization->id])
        );
        $response->assertUnauthorized();
    }

    public function test_not_found(){
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson(
            route('api.organizations.destroy', [456])
        );
        $response->assertNotFound();
    }

    public function test_owner_can_delete_organization()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $organization = Organization::factory()->create();

        $response = $this->deleteJson(
            route('api.organizations.update', [$organization->id]),
        );
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'messages',
                    fn(AssertableJson $json) => $json
                        ->where('title', 'Organization is deleted')
                        ->where('detail', 'Organization is deleted')
                        ->where('status', 200)
                )
            );
    }

    public function test_admin_cannot_delete_organization()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $organization = Organization::factory()->create();

        $response = $this->deleteJson(
            route('api.organizations.update', [$organization->id]),
        );
        $response->assertStatus(403);
    }

    public function test_common_cannot_delete_organization()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $organization = Organization::factory()->create();

        $response = $this->deleteJson(
            route('api.organizations.update', [$organization->id]),
        );
        $response->assertStatus(403);
    }
}
