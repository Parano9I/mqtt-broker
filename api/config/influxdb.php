<?php

return [
    'url' => 'http://'.env('INFLUXDB_HOST', 'localhost').':'.env('INFLUXDB_PORT', 8086),
    'token' => env('INFLUXDB_TOKEN'),
    'db_name' => env('INFLUXDB_DB', 'influxdb'),
    'org' => env('INFLUXDB_ORG'),
    'bucket' => env('INFLUXDB_BUCKET')
];
