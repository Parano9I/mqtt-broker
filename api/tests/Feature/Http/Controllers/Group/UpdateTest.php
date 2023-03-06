<?php

namespace Tests\Feature\Http\Controllers\Group;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
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
    public function test_cannot_update_with_invalid_data($formInput)
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $response = $this->patchJson(route('api.groups.update', [$group->id]), $formInput);
        $response->assertStatus(422);
    }

    public function test_unauthorized()
    {
        $response = $this->patchJson(route('api.groups.update', [1]));
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

        $response = $this->patchJson(route('api.groups.update', [24]), $data);
        $response->assertNotFound();
    }

    public function test_owner_can_update_group()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $data = [
            'title'       => 'test',
            'description' => 'test'
        ];

        $response = $this->patchJson(route('api.groups.update', [$group->id]), $data);
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

    public function test_admin_user_cannot_update_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        $group = Group::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'test',
            'description' => 'test'
        ];

        $response = $this->patchJson(route('api.groups.update', [$group->id]), $data);
        $response->assertStatus(403);
    }

    public function test_common_user_cannot_update_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        $group = Group::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'test',
            'description' => 'test'
        ];

        $response = $this->patchJson(route('api.groups.update', [$group->id]), $data);
        $response->assertStatus(403);
    }

    public function test_group_owner_can_update_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        $group = Group::factory()->hasAttached($user, ['role_id' => UserGroupRoleEnum::OWNER])->create();
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'test',
            'description' => 'test'
        ];

        $response = $this->patchJson(route('api.groups.update', [$group->id]), $data);
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

    public function test_group_admin_cannot_update_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        $group = Group::factory()->hasAttached($user, ['role_id' => UserGroupRoleEnum::ADMIN])->create();
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'test',
            'description' => 'test'
        ];

        $response = $this->patchJson(route('api.groups.update', [$group->id]), $data);
        $response->assertStatus(403);
    }

    public function test_group_common_user_cannot_update_group()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        $group = Group::factory()->hasAttached($user, ['role_id' => UserGroupRoleEnum::COMMON])->create();
        Sanctum::actingAs($user);

        $data = [
            'title'       => 'test',
            'description' => 'test'
        ];

        $response = $this->patchJson(route('api.groups.update', [$group->id]), $data);
        $response->assertStatus(403);
    }
}
