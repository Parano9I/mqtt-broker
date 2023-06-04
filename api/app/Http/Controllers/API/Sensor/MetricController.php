<?php

namespace App\Http\Controllers\API\Sensor;

use App\Http\Controllers\Controller;
use App\Http\Resources\SensorResource;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use InfluxDB2\Client;

class MetricController extends Controller
{
    public function __invoke(Request$request, Sensor $sensor)
    {
        $client = new Client([
            'url' => config('influxdb.url'),
            'token' => config('influxdb.token'),
            'org' => config('influxdb.org'),
            'bucket' => config('influxdb.bucket'),
        ]);

        if ($request->range <= 1) {
            $pointsCount = 3;
        } else {
            $pointsCount = $request->range * 0.3;
        }

        $query = sprintf('from(bucket:"%s")
            |> range(start: -%dh)
            |> filter(fn: (r) => r._measurement == "sensors" and r.sensor_id == "%s")
            |> elapsed(unit: 1s)
            |> sample(n: %d)',
            config('influxdb.bucket'), $request->range,  $sensor->uuid, $pointsCount);

        $tables = $client->createQueryApi()->query($query);

        $data = [];

        foreach ($tables as $table) {
            foreach ($table->records as $record) {
                $data[] = [
                    'value' => $record['_value'],
                    'datetime' => $record['_time']
                ];
            }
        }

        return response()->json([
            'data' => [
                'sensor' => new SensorResource($sensor),
                'metrics' => $data
            ]
        ]);
    }
}
