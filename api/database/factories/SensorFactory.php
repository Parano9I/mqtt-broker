<?php

namespace Database\Factories;

use App\Enums\SensorStatusEnum;
use App\Models\Sensor;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Query\Builder;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sensor>
 */
class SensorFactory extends Factory
{

    private array $data = [
        [
            'name'        => 'temperature',
            'description' => 'A sensor that measures the temperature in Celsius degrees'
        ],
        [
            'name'        => 'light',
            'description' => 'A sensor that measures the amount of light in lux'
        ],
        [
            'name'        => 'pressure',
            'description' => 'A sensor that measures the air pressure in kilopascals'
        ],
        [
            'name'        => 'air',
            'description' => 'A sensor that measures the quality of the air (e.g. humidity, air pollutants)'
        ]
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $sensorData = $this->data[rand(0, count($this->data) - 1)];

        return [
            'name'        => $sensorData['name'],
            'description' => $sensorData['description'],
            'secret'      => $this->faker->sha256(),
            'status'      => SensorStatusEnum::OFFLINE
        ];
    }

    public function withTopic(): Factory
    {
        return $this->state(function (array $attributes) {
            while (true) {
                $topic = Topic::inRandomOrder()->first();
                $sensor = Sensor::where('name', $attributes['name'])
                    ->where('topic_id', $topic->id)->first();

                if (!$sensor) {
                    return [
                        'topic_id' => $topic->id
                    ];
                }
            }
        });
    }
}
