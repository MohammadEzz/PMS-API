<?php

namespace Database\Factories;

use App\Models\PurchaseBill;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseBillFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseBill::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "supplier_id" => 1,
            "dealer_id" => 1,
            "billnumber" => $this->faker->randomNumber(5, true),
            "issuedate" => $this->faker->date(),
            "paymenttype" => "prepaid",
            "billstatus" => "underreview",
        ];
    }
}
