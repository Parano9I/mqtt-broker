<?php

namespace App\Http\Controllers\API\Group;

use App\Http\Controllers\Controller;
use App\Http\Resources\Group\SensorResource;
use App\Models\Group;
use App\Models\Sensor;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Kalnoy\Nestedset\QueryBuilder;

class SensorController extends Controller
{
    public function index(Group $group)
    {
        $groupId = $group->id;

        $sensors = Sensor::whereHas('topic', function ($query) use ($groupId) {
            $query->whereHas('ancestors', function ($query) use ($groupId) {
                $query->where('group_id', $groupId);
            });
        })->get()->map(function ($sensor) {
            $path = collect();
            $topic = $sensor->topic;
            while ($topic) {
                $path->prepend($topic->name);
                $topic = $topic->parent;
            }
            $sensor->setAttribute('path', $path->implode('/'));
            return $sensor;
        });

        return response()->json([
            'data' => SensorResource::collection($sensors)
        ]);
    }
}
