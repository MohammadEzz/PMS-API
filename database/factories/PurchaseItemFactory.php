<?php

namespace Database\Factories;

use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PurchaseItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "quantity" => 50,
            "purchaseprice" => 9.5,
            "sellprice" => 10,
            "tax" => 0,
            "discount" => 5,
            "expiredate" => $this->faker->date(),
        ];
    }
}
