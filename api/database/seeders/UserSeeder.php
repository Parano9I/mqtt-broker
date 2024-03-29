<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'role' => UserRoleEnum::OWNER
        ]);
        User::factory(20)->create();
    }
}
