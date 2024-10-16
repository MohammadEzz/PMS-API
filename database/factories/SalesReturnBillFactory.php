<?php

namespace Database\Factories;

use App\Models\SalesReturnBill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesReturnBillFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesReturnBill::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'total' => 0.0,
            'discount' => 0.0,
            'paymentamount' => 0.0,
            'editable' => 1,
            'billstatus' => 'underreview',
            'created_by' => 1,
        ];
    }
}
