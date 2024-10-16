<?php

namespace Database\Factories;

use App\Models\PurchaseReturnBill;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseReturnBillFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseReturnBill::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "issuedate" => $this->faker->date()
        ];
    }
}
