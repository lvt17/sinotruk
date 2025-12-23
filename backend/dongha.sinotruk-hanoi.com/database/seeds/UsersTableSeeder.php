<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->delete();
        \App\User::create([
            'name' => 'admin',
            'full_name' => 'admin',
            'phone' => '0987654321',
            'password' => bcrypt('123456789'),
            'admin' => true,
            'money' => 1000000,
        ]);
    }
}
