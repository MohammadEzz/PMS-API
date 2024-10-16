<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = [
            ['name' => 'mohasen', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ali', 'created_at' => now(), 'updated_at' => now()],   
            ['name' => 'mahmoud', 'created_at' => now(), 'updated_at' => now()],   
            ['name' => 'fatma', 'created_at' => now(), 'updated_at' => now()],   
            ['name' => 'jana', 'created_at' => now(), 'updated_at' => now()],   
            ['name' => 'tamer', 'created_at' => now(), 'updated_at' => now()],   
        ];

        DB::table('clients')->insert($clients);
    }
}
