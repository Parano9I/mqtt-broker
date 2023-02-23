<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Enums\UserRoleEnum;
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
        $response = $this->getJson(route('api.users.destroy'));
        $response->assertUnauthorized();
    }

    public function test_cannot_delete_owner()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('api.users.destroy'));
        $response->assertStatus(403);
    }

    public function test_success_delete_admin()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('api.users.destroy'));
        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json->has('message'),
                fn(AssertableJson $json) => $json
                    ->where('message', 'User deleted')
                    ->where(
                        'detail',
                        'User deleted from organization'
                    )
                    ->where('status', 200)
            );
    }
    public function test_success_delete_common()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('api.users.destroy'));
        $response
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json->has('message'),
                fn(AssertableJson $json) => $json
                    ->where('message', 'User deleted')
                    ->where(
                        'detail',
                        'User deleted from organization'
                    )
                    ->where('status', 200)
            );
    }

}
