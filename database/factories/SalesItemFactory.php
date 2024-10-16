<?php

namespace Database\Factories;

use App\Models\SalesItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SalesItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'quantity' => 1,
            'discount' => 0,
        ];
    }
}
