<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
		//$this->call(UsersTableSeeder::class);
        //$this->call(OptionsTableSeeder::class);
        //$this->call(JobsTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
    }
}
