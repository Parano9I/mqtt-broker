<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\StoreRequest;
use App\Http\Requests\Topic\UpdateRequest;
use App\Http\Resources\TopicResource;
use App\Models\Group;
use App\Models\Topic;

class TopicController extends Controller
{
    public function index()
    {

    }

    public function show()
    {
    }

    public function store(StoreRequest $request, Group $group)
    {
        $this->authorize('create', [Topic::class, $group]);
        $request->validated();

        $parentId = $request->get('parent_id');

        $topic = Topic::create([
            'name' => $request->get('name'),
        ]);

        if (!$parentId) {
            $topic->group()->associate($group)->save();
        } else {
            $topic->parent()->associate($parentId)->save();
        }

        return response()->json([
            'data' => new TopicResource($topic)
        ]);
    }

    public function update(UpdateRequest $request, Group $group, Topic $topic)
    {
        $this->authorize('update', [Topic::class, $group]);
        $request->validated();

        $topic->update([
            'name' => $request->get('name')
        ]);

        $parentId = $request->get('parent_id');

        if ($parentId && $parentId !== $topic->id) {
            $topic->setParentId($parentId)->save();
        }

        return response()->json([
            'data' => new TopicResource($topic)
        ]);
    }

    public function destroy(Group $group, Topic $topic)
    {
        $this->authorize('delete', [Topic::class, $group]);

        $topic->delete();

        return response()->json([], 204);
    }
}
