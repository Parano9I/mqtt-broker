<?php

namespace App\Http\Controllers\API\Sensor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sensor\StoreRequest;
use App\Http\Requests\Sensor\UpdateRequest;
use App\Http\Resources\SensorResource;
use App\Models\Group;
use App\Models\Sensor;
use App\Models\Topic;
use App\Services\SensorService;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index()
    {
    }

    public function show()
    {
    }

    public function store(StoreRequest $request, Group $group, Topic $topic, SensorService $service)
    {
        $request->validated();
        $this->authorize('create', [Sensor::class, $group]);

        $sensor = new Sensor();
        $sensor->name = $request->get('name');
        $sensor->description = $request->get('description');
        $sensor->secret = $service->generateSecret();
        $sensor->topic()->associate($topic);
        $sensor->save();

        return response()->json([
            'data' => new SensorResource($sensor)
        ]);
    }

    public function update(UpdateRequest $request, Group $group, Topic $topic, Sensor $sensor)
    {
        $data = $request->validated();
        $this->authorize('update', [Sensor::class, $group]);

        $sensor->updateOrFail($data);

        return response()->json([
            'data' => new SensorResource($sensor)
        ]);
    }

    public function destroy()
    {
    }
}
