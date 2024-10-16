<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::table('roles')->insert(
        [
            ["name"=>"admin", "editable"=>0, "visible" => 1],
            ["name"=>"manager", "editable"=>0, "visible" => 1],
            ["name"=>"assistant", "editable"=>0, "visible" => 1],
            ["name"=>"pharmacist", "editable"=>0, "visible" => 1],
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
