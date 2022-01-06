<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            "name" => "Muhammad Prottoy",
            "username" => "b3d0na",
            "email" => "prottoyLancer@gmail.com",
            "password" => bcrypt("password"),
        ]);
        \App\Models\User::factory(10)->create();
    }
}