<?php

namespace App\Http\Controllers\API\Sensor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Group\SensorResource;
use App\Models\Group;
use App\Models\Sensor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userGroups = $user->groups();

        $sensors = Sensor::whereHas('topic', function ($query) use ($userGroups) {
            $query->whereHas('ancestors', function ($query) use ($userGroups) {
                $query->whereIn('group_id', $userGroups->pluck('id'));
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
