<?php

namespace Tests\Feature\Http\Controllers\Sensor;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\Sensor;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{

    use RefreshDatabase;

    private Group $group;

    private Topic $topic;

    private Sensor $sensor;

    public function setUp(): void
    {
        parent::setUp();

        $this->group = Group::factory()->create();
        $topics = Topic::create([
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
        $this->topic = Topic::whereName('topic_1.3')->first();
        $this->topic->group()->associate($this->group)->save();

        $this->sensor = Sensor::factory()->create(['topic_id' => $this->topic->id]);
    }

    public function test_unauthorized()
    {
        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid])
        );
        $response->assertUnauthorized();
    }

    public function validation_invalid_casts()
    {
        return [
            'empty_name'             => [
                [
                    'name'       => '',
                    'description' => 'description',
                ]
            ],
            'name_int_type'          => [
                [
                    'name'       => 0,
                    'description' => 'description',
                ]
            ],
            'name_float_type'        => [
                [
                    'name'       => 0.0,
                    'description' => 'description',
                ]
            ],
            'name_arr_type'          => [
                [
                    'name'       => [],
                    'description' => 'description',
                ]
            ],
            'empty_description'      => [
                [
                    'name'       => 'name',
                    'description' => '',
                ]
            ],
            'description_int_type'   => [
                [
                    'name'       => 'name',
                    'description' => 0,
                ]
            ],
            'description_float_type' => [
                [
                    'name'       => 'name',
                    'description' => 0.0,
                ]
            ],
            'description_arr_type'   => [
                [
                    'name'       => 'name',
                    'description' => [],
                ]
            ],
            'topic_id_arr_type'      => [
                [
                    'name'       => 'name',
                    'description' => 'dkfdsl;fkfl;kfl;skf',
                    'topic_id'    => []
                ]
            ],
            'topic_id_string_type'    => [
                [
                    'name'       => 'name',
                    'description' => 'dkfdsl;fkfl;kfl;skf',
                    'topic_id'    => 'sdsd'
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
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $formInput
        );
        $response->assertStatus(422);
    }

    public function test_validation_topic_id_not_exists()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $data = [
            'name'       => 'sensorName',
            'description' => Str::random(40),
            'topic_id'    => $this->topic->id + 100
        ];

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $data
        );
        $response->assertStatus(422);
    }

    public function test_validation_topic_id_not_different_old_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $data = [
            'name'       => $this->sensor->name,
            'description' => Str::random(40),
            'topic_id'    => $this->topic->id
        ];

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $data
        );
        $response->assertStatus(200);
    }

    public function test_owner_can_update_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $data = [
            'name'       => 'sensorName',
            'description' => Str::random(40),
        ];

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $data
        );
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'sensor')
                        ->whereType('id', 'string')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('name', $data['name'])
                            ->where('description', $data['description'])
                            ->where('status', $this->sensor->status->getLabel())
                        )
                )
            );
    }

    public function test_admin_cannot_update_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $data = [
            'name'       => 'sensorName',
            'description' => Str::random(40),
        ];

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $data
        );
        $response->assertStatus(403);
    }

    public function test_common_user_cannot_update_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $data = [
            'name'       => 'sensorName',
            'description' => Str::random(40),
        ];

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $data
        );
        $response->assertStatus(403);
    }

    public function test_group_owner_can_update_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $data = [
            'name'       => 'sensorName',
            'description' => Str::random(40),
        ];

        $this->group->users()->attach($user, ['role_id' => UserGroupRoleEnum::OWNER]);

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $data
        );
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'sensor')
                        ->whereType('id', 'string')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('name', $data['name'])
                            ->where('description', $data['description'])
                            ->where('status', $this->sensor->status->getLabel())
                        )
                )
            );
    }

    public function test_group_admin_can_update_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $data = [
            'name'       => 'sensorName',
            'description' => Str::random(40),
        ];

        $this->group->users()->attach($user, ['role_id' => UserGroupRoleEnum::ADMIN]);

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $data
        );
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'sensor')
                        ->whereType('id', 'string')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('name', $data['name'])
                            ->where('description', $data['description'])
                            ->where('status', $this->sensor->status->getLabel())
                        )
                )
            );
    }

    public function test_group_common_user_cannot_update_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $data = [
            'name'       => 'sensorName',
            'description' => Str::random(40),
        ];

        $this->group->users()->attach($user, ['role_id' => UserGroupRoleEnum::COMMON]);

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $data
        );
        $response->assertStatus(403);
    }

    public function test_can_update_topic_for_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $newTopic = Topic::whereName('topic_1.3.2')->first();

        $data = [
            'name'       => $this->sensor->name,
            'description' => $this->sensor->description,
            'topic_id'    => $newTopic->id
        ];

        $this->group->users()->attach($user, ['role_id' => UserGroupRoleEnum::ADMIN]);

        $response = $this->patchJson(
            route('api.groups.topics.sensors.update', [$this->group->id, $this->topic->id, $this->sensor->uuid]),
            $data
        );
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'sensor')
                        ->whereType('id', 'string')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('name', $data['name'])
                            ->where('description', $data['description'])
                            ->where('status', $this->sensor->status->getLabel())
                        )
                )
            );
    }
}
