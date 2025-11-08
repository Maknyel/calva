<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'fname' => 'calva',
            'lname' => 'pharma',
            'email' => 'calvapharma@gmail.com',
            'contact_number' => '09123456789',
            'password' => Hash::make('Abc123456'),
            'role_id' => 1,
        ]);
    }
}
