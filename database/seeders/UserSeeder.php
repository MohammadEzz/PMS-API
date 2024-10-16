<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
            'firstname' => "Admin",
            'middlename' => "",
            'lastname' => "Admin",
            'gender' => 'male',
            'birthdate' => "1988-09-15",
            'country' => 1,
            'city' => 1,
            'address' => "Part 2, El Saeed Nassar st, appartment 36, floor 4, flat 8",
            'nationalid' => "00000000000000",
            'passportnum' => "0000000",
            'username' => "admin",
            'email' => "admin@pms.com",
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'status' => 1,
            'visible' => 'visible',
            'editable' => 0,
            'note' => "Some note related to user",
            'created_by' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
