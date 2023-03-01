<?php

namespace App\Http\Controllers\API\Group;

use App\Enums\UserGroupRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\StoreRequest;
use App\Http\Resources\Group\GroupResource;
use App\Models\Group;

class GroupController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => GroupResource::collection(Group::paginate(10))
        ]);
    }

    public function show(Group $group)
    {
        return response()->json([
            'data' => new GroupResource($group)
        ]);
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('create', new Group());

        $data = $request->validated();
        $user = $request->user();

        $group = Group::create($data);
        $group->users()->attach($user, ['role_id' => UserGroupRoleEnum::OWNER]);

        return response()->json([
            'data' => new GroupResource($group)
        ]);
    }

    public function update(StoreRequest $request, Group $group)
    {
        $this->authorize('update', $group);

        $data = $request->validated();

        $group->update($data);

        return response()->json([
            'data' => new GroupResource($group)
        ]);
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);

        $group->deleteOrFail();

        return response()->json([], 204);
    }
}
