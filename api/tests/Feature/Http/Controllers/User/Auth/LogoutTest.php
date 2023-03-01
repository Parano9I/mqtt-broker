<?php

namespace Tests\Feature\Http\Controllers\User\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized()
    {
        $response = $this->getJson(route('api.auth.logout'));
        $response->assertUnauthorized();
    }

    public function test_success()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('api.auth.logout'));
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'messages',
                    fn(AssertableJson $json) => $json->where('title', 'Logged out')
                        ->where('detail', 'Logged out is success')
                        ->where('status', 200)
                )
            );;
    }
}
