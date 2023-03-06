<?php

namespace Tests\Feature\Http\Controllers\Topic;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private Topic $topics;
    private Group $group;

    public function setUp(): void
    {
        parent::setUp();
        $this->group = Group::factory()->create();
        $this->topics = Topic::create([
            'name'     => 'topic_1',
            'children' => [
                ['name' => 'topic_1.1'],
                ['name' => 'topic_1.2'],
                [
                    'name'     => 'topic_1.3',
                    'children' => [
                        ['name' => 'topic_1.3.1'],
                        ['name' => 'topic_1.3.2']
                    ]
                ],
            ]
        ]);
    }

    public function test_unauthorized()
    {
        $response = $this->patchJson(route('api.groups.topics.update', [$this->group->id, $this->topics->id]));
        $response->assertUnauthorized();
    }

    public function validation_invalid_casts()
    {
        return [
            'empty_name' => [
                ['name' => ''],
            ],
            'name_int_type' => [
                ['name' => 0],
            ],
            'name_bool_type' => [
                ['name' => true],
            ],
            'name_arr_type' => [
                ['name' => []],
            ],
            'name_float_type' => [
                ['name' => 0.0],
            ],
            'parent_id_string_type' => [
                ['parent_id' => '1', 'name' => 'topic_1'],
            ],
            'parent_id_bool_type' => [
                ['parent_id' => true, 'name' => 'topic_1'],
            ],
            'parent_id_arr_type' => [
                ['parent_id' => [], 'name' => 'topic_1'],
            ],
            'parent_id_float_type' => [
                ['parent_id' => 0.0, 'name' => 'topic_1'],
            ],
            'parent_id_not_exists' => [
                ['parent_id' => 0, 'name' => 'topic_1'],
            ]
        ];
    }

    /**
     * @test
     * @dataProvider validation_invalid_casts
     */
    public function test_cannot_store_with_invalid_data($formInput)
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $group = Group::factory()->create();

        $response = $this->patchJson(route('api.groups.topics.update', [$group->id, $this->topics->id]), $formInput);
        $response->assertStatus(422);
    }

    public function test_group_should_by_exists()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $data = [
            'name' => 'topic_1',
        ];

        $response = $this->patchJson(route('api.groups.topics.update', [12, $this->topics->id]), $data);
        $response->assertNotFound();
    }

    public function test_topic_should_by_exists()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $data = [
            'name' => 'topic_1'
        ];

        $response = $this->patchJson(route('api.groups.topics.update', [$this->group->id, 86]), $data);
        $response->assertNotFound();
    }

    public function test_owner_can_update_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $data = [
            'name' => 'topic_1'
        ];

        $response = $this->patchJson(route('api.groups.topics.update', [$this->group->id, $this->topics->id]), $data);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'topic')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('parent_id', null)
                            ->where('name', $data['name'])
                        )
                )
            );
    }

    public function test_user_should_be_in_group_if_not_owner()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $data = [
            'name' => 'topic_1'
        ];

        $response = $this->patchJson(route('api.groups.topics.update', [$this->group->id, $this->topics->id]), $data);
        $response
            ->assertStatus(403);
    }

    public function test_group_owner_can_update_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $this->group->users()
            ->attach($user, ['role_id' => UserGroupRoleEnum::OWNER]);

        $data = [
            'name' => 'topic_1'
        ];

        $response = $this->patchJson(route('api.groups.topics.update', [$this->group->id, $this->topics->id]), $data);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'topic')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('parent_id', null)
                            ->where('name', $data['name'])
                        )
                )
            );
    }

    public function test_group_admin_can_update_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $this->group->users()
            ->attach($user, ['role_id' => UserGroupRoleEnum::ADMIN]);

        $data = [
            'name' => 'topic_1'
        ];

        $response = $this->patchJson(route('api.groups.topics.update', [$this->group->id, $this->topics->id]), $data);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'topic')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('parent_id', null)
                            ->where('name', $data['name'])
                        )
                )
            );
    }

    public function test_group_common_user_cannot_update_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $this->group->users()
            ->attach($user, ['role_id' => UserGroupRoleEnum::COMMON]);

        $data = [
            'name' => 'topic_1'
        ];

        $response = $this->patchJson(route('api.groups.topics.update', [$this->group->id, $this->topics->id]), $data);
        $response->assertStatus(403);
    }

    public function test_can_change_parent_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $targetTopic = Topic::where('name', 'topic_1.3')->first();
        $newParentTopic  = Topic::where('name', 'topic_1.2')->first();

        $data = [
            'name' => 'topic_1.3',
            'parent_id' => $newParentTopic->id
        ];

        $response = $this->patchJson(route('api.groups.topics.update', [$this->group->id, $targetTopic->id]), $data);
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'topic')
                        ->whereType('id', 'integer')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('parent_id', $newParentTopic->id)
                            ->where('name', $data['name'])
                        )
                )
            );
    }
}
