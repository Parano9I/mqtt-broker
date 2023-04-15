<?php

namespace Tests\Feature\Http\Controllers\Topic;

use App\Enums\UserGroupRoleEnum;
use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTest extends TestCase
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
        $response = $this->deleteJson(route('api.groups.topics.destroy', [$this->group->id, $this->topics->id]));
        $response->assertUnauthorized();
    }

    public function test_group_should_by_exists()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('api.groups.topics.destroy', [12, $this->topics->id]));
        $response->assertNotFound();
    }

    public function test_topic_should_by_exists()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('api.groups.topics.destroy', [$this->group->id, $this->topics->id + 20]));
        $response->assertNotFound();
    }

    public function test_user_should_be_in_group_if_not_owner()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('api.groups.topics.destroy', [$this->group->id, $this->topics->id]));
        $response
            ->assertStatus(403);
    }

    public function test_owner_can_delete_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::OWNER]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('api.groups.topics.destroy', [$this->group->id, $this->topics->id]));
        $response->assertNoContent();
        $this->assertDatabaseEmpty('topics');
    }

    public function test_group_owner_can_delete_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $this->group->users()
            ->attach($user, ['role_id' => UserGroupRoleEnum::OWNER]);

        $response = $this->deleteJson(route('api.groups.topics.destroy', [$this->group->id, $this->topics->id]));
        $response->assertNoContent();
        $this->assertDatabaseEmpty('topics');
    }

    public function test_group_admin_can_delete_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $this->group->users()
            ->attach($user, ['role_id' => UserGroupRoleEnum::ADMIN]);

        $response = $this->deleteJson(route('api.groups.topics.destroy', [$this->group->id, $this->topics->id]));
        $response->assertNoContent();
        $this->assertDatabaseEmpty('topics');
    }

    public function test_group_common_user_cannot_delete_topic()
    {
        $user = User::factory()->create(['role' => UserRoleEnum::ADMIN]);
        Sanctum::actingAs($user);

        $this->group->users()
            ->attach($user, ['role_id' => UserGroupRoleEnum::COMMON]);

        $response = $this->deleteJson(route('api.groups.topics.destroy', [$this->group->id, $this->topics->id]));
        $response->assertStatus(403);
    }
}
