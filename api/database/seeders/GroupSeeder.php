<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{

    private array $data = [
        [
            'title'        => 'KrNU',
            'description' => 'Kremenchuk National University named after Mykhaylo Ostrogradskiy (KrNU) is a state non-commercial educational institution. KrNU is located in the city of Kremenchug, Ukraine.'
        ],
        [
            'title'        => 'KhNU',
            'description' => 'Kharkiv National University V N Karazin (KhNU) is a state non-commercial educational institution. KhNU is located in the city of Kharkiv, Ukraine. KhNU is a member of the European University Association (EUA).'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $group) {
            Group::create($group);
        }
    }
}
