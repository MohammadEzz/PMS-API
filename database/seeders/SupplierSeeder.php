<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $countries = [
            ['name' => 'Ibnsina Pharma', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Almutahida', 'created_at' => now(), 'updated_at' => now()],   
            ['name' => 'A.M.Group', 'created_at' => now(), 'updated_at' => now()],   
        ];

        DB::table('suppliers')->insert($countries);
    }
}
