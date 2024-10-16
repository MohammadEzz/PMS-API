<?php

namespace Database\Factories;

use App\Models\SalesBill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesBillFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesBill::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'paidstatus' => 'paid',
            'total' => 0,
            'discount' => 0,
            'paymentamount' => 0,
            'editable' => 1,
            'billstatus' => 'underreview',
            'created_by' => 1,
        ];
    }
}
