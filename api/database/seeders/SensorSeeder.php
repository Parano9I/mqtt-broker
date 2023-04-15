<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Sensor;
use App\Models\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SensorSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sensor::factory(Topic::count() - 5)->withTopic()->create();
    }
}
