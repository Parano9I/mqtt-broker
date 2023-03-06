<?php

namespace Tests\Feature\Http\Controllers\Group;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTest extends TestCase
{

    use RefreshDatabase;

    public function validation_invalid_casts()
    {
        return [
            'empty_name'             => [
                [
                    'title'        => '',
                    'description' => 'description',
                ]
            ],
            'name_int_type'          => [
                [
                    'title'        => 0,
                    'description' => 'description',
                ]
            ],
            'name_float_type'        => [
                [
                    'title'        => 0.0,
                    'description' => 'description',
                ]
            ],
            'name_arr_type'          => [
                [
                    'title'        => [],
                    'description' => 'description',
                ]
            ],
            'empty_description'      => [
                [
                    'title'        => 'title',
                    'description' => '',
                ]
            ],
            'description_int_type'   => [
                [
                    'title'        => 'title',
                    'description' => 0,
                ]
            ],
            'description_float_type' => [
                [
                    'title'        => 'title',
                    'description' => 0.0,
                ]
            ],
            'description_arr_type'   => [
                [
                    'title'        => 'title',
                    'description' => [],
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider validation_invalid_casts
     */
    public function test_cannot_store_with_invalid_data($formInput)
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.groups.store'), $formInput);
        $response->assertStatus(422);
    }

    public function test_unauthorized()
    {
        $response = $this->postJson(route('api.groups.store'));
        $response->assertUnauthorized();
    }

    public function test_owner_can_create_group()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'Test group',
            'description' => 'Test group description'
        ];

        $response = $this->postJson(route('api.groups.store'), $data);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'group')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('title', $data['title'])
                            ->where('description', $data['description'])
                        )
                )
            );
    }

    public function test_that_creator_will_by_group_owner()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'Test group',
            'description' => 'Test group description'
        ];

        $response = $this->postJson(route('api.groups.store'), $data);
        $response->assertOk();
        $this->assertDatabaseHas('groups_users', [
            'group_id' => $response->original['data']->id,
            'user_id'  => $user->id,
            'role_id'  => UserGroupRoleEnum::OWNER
        ]);
    }

    public function test_cannot_create_group_with_duplicate_title()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $data = [
            'title'       => $group->title,
            'description' => $group->description
        ];

        $response = $this->postJson(route('api.groups.store'), $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => Lang::get('validation.unique', ['attribute' => 'title']),
            ]);
    }

    public function test_admin_can_create_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'Test group',
            'description' => 'Test group description'
        ];

        $response = $this->postJson(route('api.groups.store'), $data);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'group')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('title', $data['title'])
                            ->where('description', $data['description'])
                        )
                )
            );
    }

    public function test_common_user_cannot_create_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'Test group',
            'description' => 'Test group description'
        ];

        $response = $this->postJson(route('api.groups.store'), $data);
        $response->assertStatus(403);
    }
}
