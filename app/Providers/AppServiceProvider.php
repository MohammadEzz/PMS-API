<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'sales' => 'App\\Models\\SalesBill',
            'returns' => 'App\\Models\\SalesReturnBill',
            'supplier' => 'App\\Models\\Supplier',
            'client' => 'App\\Models\\Client',
        ]);
    }
}
