<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{

    private array $data = [];

    public function __construct()
    {
        $this->data = [
            [
                'name'     => 'Building 1',
                'group_id' => $this->getRandomGroup()->id,
                'children' => [
                    [
                        'name'     => 'First floor',
                        'children' => [
                            ['name' => '1115'],
                            ['name' => '1123']
                        ]
                    ],
                    [
                        'name'     => 'Second floor',
                        'children' => [
                            ['name' => '1223'],
                            ['name' => '1208'],
                            ['name' => '1210']
                        ]
                    ],
                ],
            ],
            [
                'name'     => 'Building 2',
                'group_id' => $this->getRandomGroup()->id,
                'children' => [
                    [
                        'name'     => 'First floor',
                        'children' => [
                            ['name' => '2101'],
                            ['name' => '2112']
                        ]
                    ],
                    [
                        'name'     => 'Second floor',
                        'children' => [
                            ['name' => '2205'],
                            ['name' => '2239'],
                            ['name' => '2274']
                        ]
                    ],
                ],
            ]
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $topicData) {
            Topic::create($topicData);
        }
    }

    public function getRandomGroup()
    {
        return Group::inRandomOrder()->first();
    }
}
