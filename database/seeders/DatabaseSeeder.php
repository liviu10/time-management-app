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
        $this->call([
            UserRoleTypeSeeder::class,
            UserSeeder::class,
            ErrorAndNotificationSystemSeeder::class,
        ]);
    }
}
