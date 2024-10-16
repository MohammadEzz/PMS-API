<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DealerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dealers = [
            ['supplier_id' => 1, 'name' => "Kareem Adel", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 1, 'name' => "Scarlett Adam", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 1, 'name' => "John David", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 1, 'name' => "Richard William", 'created_at' => now(), 'updated_at' => now()],

            ['supplier_id' => 2, 'name' => "Luna Jacob", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 2, 'name' => "Nora Ahmed", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 2, 'name' => "Joseph Charles", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 2, 'name' => "Amelia David", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 2, 'name' => "Daniel James", 'created_at' => now(), 'updated_at' => now()],

            ['supplier_id' => 3, 'name' => "Ali khan", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 3, 'name' => "Jone William", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 3, 'name' => "Sophia Mike", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 3, 'name' => "Eleanor Thomas", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 3, 'name' => "Donald Anthony", 'created_at' => now(), 'updated_at' => now()],
            ['supplier_id' => 3, 'name' => "Mia Jeffrey", 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('dealers')->insert($dealers);
    }
}
