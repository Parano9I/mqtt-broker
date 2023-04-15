<?php

namespace Database\Seeders;

use App\Enums\UserGroupRoleEnum;
use App\Models\Group;
use App\Models\User;
use App\Providers\FakerServiceProvider;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{

    protected Generator $faker;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (User::all() as $user) {
            $group = Group::inRandomOrder()->first();

            $user->groups()->attach(
                $group,
                ['role_id' => $this->faker->groupRole($group)]
            );
        }
    }
}
