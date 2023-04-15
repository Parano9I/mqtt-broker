<?php

namespace Tests\Feature\Http\Controllers\Sensor;

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


class CreateTest extends TestCase
{

    use RefreshDatabase;

    private Group $group;

    private Topic $topic;

    private $sensorData = [
        'name'        => 'sensor1',
        'description' => 'This description is for sensor 1: Sensor 1 shows the room temperature'
    ];

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
    }

    public function test_unauthorized()
    {
        $response = $this->postJson(route('api.groups.topics.sensors.store', [$this->group->id, $this->topic->id]));
        $response->assertUnauthorized();
    }

    public function validation_invalid_casts()
    {
        return [
            'empty_name'             => [
                [
                    'title'       => '',
                    'description' => 'description',
                ]
            ],
            'name_int_type'          => [
                [
                    'title'       => 0,
                    'description' => 'description',
                ]
            ],
            'name_float_type'        => [
                [
                    'title'       => 0.0,
                    'description' => 'description',
                ]
            ],
            'name_arr_type'          => [
                [
                    'title'       => [],
                    'description' => 'description',
                ]
            ],
            'empty_description'      => [
                [
                    'title'       => 'title',
                    'description' => '',
                ]
            ],
            'description_int_type'   => [
                [
                    'title'       => 'title',
                    'description' => 0,
                ]
            ],
            'description_float_type' => [
                [
                    'title'       => 'title',
                    'description' => 0.0,
                ]
            ],
            'description_arr_type'   => [
                [
                    'title'       => 'title',
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
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $response = $this->postJson(
            route('api.groups.topics.sensors.store', [$this->group->id, $this->topic->id]),
            $formInput
        );
        $response->assertStatus(422);
    }

    public function test_owner_can_create_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $response = $this->postJson(
            route('api.groups.topics.sensors.store', [$this->group->id, $this->topic->id]),
            $this->sensorData
        );
        $response
            ->assertOk()
            ->assertJson(
            fn(AssertableJson $json) => $json->has(
                'data',
                fn(AssertableJson $json) => $json->where('type', 'sensor')
                    ->whereType('id', 'string')
                    ->has('attributes', fn(AssertableJson $json) => $json
                        ->where('name', $this->sensorData['name'])
                        ->where('description', $this->sensorData['description'])
                        ->where('status', 'Offline')
                    )
            )
        );
    }

    public function test_admin_cannot_create_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->postJson(
            route('api.groups.topics.sensors.store', [$this->group->id, $this->topic->id]),
            $this->sensorData
        );
        $response->assertStatus(403);
    }

    public function test_common_user_cannot_create_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $response = $this->postJson(
            route('api.groups.topics.sensors.store', [$this->group->id, $this->topic->id]),
            $this->sensorData
        );
        $response->assertStatus(403);
    }

    public function test_group_owner_can_create_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $this->group->users()->attach($user, ['role_id' => UserGroupRoleEnum::OWNER]);

        $response = $this->postJson(
            route('api.groups.topics.sensors.store', [$this->group->id, $this->topic->id]),
            $this->sensorData
        );
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'sensor')
                        ->whereType('id', 'string')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('name', $this->sensorData['name'])
                            ->where('description', $this->sensorData['description'])
                            ->where('status', 'Offline')
                        )
                )
            );
    }

    public function test_group_admin_can_create_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $this->group->users()->attach($user, ['role_id' => UserGroupRoleEnum::ADMIN]);

        $response = $this->postJson(
            route('api.groups.topics.sensors.store', [$this->group->id, $this->topic->id]),
            $this->sensorData
        );
        $response
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has(
                    'data',
                    fn(AssertableJson $json) => $json->where('type', 'sensor')
                        ->whereType('id', 'string')
                        ->has('attributes', fn(AssertableJson $json) => $json
                            ->where('name', $this->sensorData['name'])
                            ->where('description', $this->sensorData['description'])
                            ->where('status', 'Offline')
                        )
                )
            );
    }
    public function test_group_common_user_cannot_create_sensor()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::COMMON]);
        Sanctum::actingAs($user);

        $this->group->users()->attach($user, ['role_id' => UserGroupRoleEnum::COMMON]);

        $response = $this->postJson(
            route('api.groups.topics.sensors.store', [$this->group->id, $this->topic->id]),
            $this->sensorData
        );
        $response->assertStatus(403);
    }
}
