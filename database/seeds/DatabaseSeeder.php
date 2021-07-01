<?php

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
        // we create the roles first then we create users which is linked to roles
        $this->call(LaratrustSeeder::class);
        $this->call(UserSeeder::class);
    }
}
