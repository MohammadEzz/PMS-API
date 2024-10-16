<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            OptionSeeder::class,
            RoleSeeder::class,
            CountriesSeeder::class,
            CitySeeder::class,
            UserSeeder::class,
            SupplierSeeder::class,
            DealerSeeder::class,
            ClientSeeder::class,
        ]);
    }
}
