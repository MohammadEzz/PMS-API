<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('cities')->truncate();
        DB::table('cities')->insert(
        [
            ["name"=>"10th of Ramadan", "areacode"=>15],
            ["name"=>"Alexandria", "areacode"=>3],
            ["name"=>"Assiout", "areacode"=>88],
            ["name"=>"Aswan", "areacode"=>97],
            ["name"=>"Qalyubia", "areacode"=>13],
            ["name"=>"Beni Suef", "areacode"=>82],
            ["name"=>"Cairo", "areacode"=>2],
            ["name"=>"Gizah", "areacode"=>2],
            ["name"=>"Damanhohr", "areacode"=>45],
            ["name"=>"Damiette", "areacode"=>57],
            ["name"=>"Al Arish", "areacode"=>68],
            ["name"=>"Al Tour", "areacode"=>69],
            ["name"=>"Fayoum", "areacode"=>84],
            ["name"=>"Hurghada", "areacode"=>65],
            ["name"=>"Ismailia", "areacode"=>64],
            ["name"=>"Kafer El Sheik", "areacode"=>47],
            ["name"=>"Luxor", "areacode"=>95],
            ["name"=>"Mansoura", "areacode"=>50],
            ["name"=>"Manufia", "areacode"=>48],
            ["name"=>"Marsa Matrrouh", "areacode"=>46],
            ["name"=>"Minia", "areacode"=>86],
            ["name"=>"Port Said", "areacode"=>66],
            ["name"=>"Qina", "areacode"=>96],
            ["name"=>"Red Sea", "areacode"=>65],
            ["name"=>"Sohag", "areacode"=>93],
            ["name"=>"Suez", "areacode"=>62],
            ["name"=>"Tanta", "areacode"=>40],
            ["name"=>"Wadi El Gedid", "areacode"=>92],
            ["name"=>"Zagazig", "areacode"=>55],
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
