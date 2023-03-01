<?php

namespace Tests\Feature\Http\Controllers\Organization;

use App\Enums\UserRoleEnum;
use App\Models\Organization;
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

    public function test_empty_post_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'name'        => '',
            'description' => ''
        ];

        $response = $this->postJson(route('api.organizations.store'), $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name'        => Lang::get('validation.required', ['attribute' => 'name']),
                'description' => Lang::get('validation.required', ['attribute' => 'description']),
            ]);
    }

    public function test_unauthorized()
    {
        $response = $this->getJson(route('api.organizations.store'));
        $response->assertUnauthorized();
    }

    public function test_owner_can_create_first_organization()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'name'        => 'Test',
            'description' => 'Test description'
        ];

        $response = $this->postJson(route('api.organizations.store'), $data);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'organization')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('name', $data['name'])
                            ->where('description', $data['description'])
                        )
                )
            );
    }

    public function test_admin_cannot_create_first_organization()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $data = [
            'name'        => 'Test',
            'description' => 'Test description'
        ];

        $response = $this->postJson(route('api.organizations.store'), $data);
        $response->assertStatus(403);
    }

    public function test_common_user_cannot_create_first_organization()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $data = [
            'name'        => 'Test',
            'description' => 'Test description'
        ];

        $response = $this->postJson(route('api.organizations.store'), $data);
        $response->assertStatus(403);
    }

    public function test_cannot_create_second_organization()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Organization::factory()->create();

        $data = [
            'name'        => 'Test',
            'description' => 'Test description'
        ];

        $response = $this->postJson(route('api.organizations.store'), $data);
        $response->assertJson(
            fn(AssertableJson $json) => $json->has(
                'errors',
                fn(AssertableJson $json) => $json
                    ->where('title', 'Conflict')
                    ->where('detail', 'There can be only one organization')
                    ->where('status', 409)

            )
        );
    }
}
