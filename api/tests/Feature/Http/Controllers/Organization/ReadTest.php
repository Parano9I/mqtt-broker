<?php

namespace Tests\Feature\Http\Controllers\Organization;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReadTest extends TestCase
{

    use RefreshDatabase;

    public function test_unauthorized()
    {
        $response = $this->getJson(
            route('api.organizations.index')
        );
        $response->assertUnauthorized();
    }

    public function test_not_found()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(
            route('api.organizations.index')
        );
        $response->assertNotFound();
    }

    public function test_success_get_organization()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $organization = Organization::factory()->create();

        $response = $this->getJson(
            route('api.organizations.index')
        );
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'organization')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('name', $organization->name)
                            ->where('description', $organization->description)
                        )
                )
            );
    }
}
