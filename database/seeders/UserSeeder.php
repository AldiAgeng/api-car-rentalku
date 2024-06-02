<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        DB::table('users')->insert([
            'name' => 'user',
            'address' => 'Jl. Ahmad Yani No. 1',
            'phone' => '081234567890',
            'driver_license' => 'ABCD123456',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'member'
        ]);
    }
}
