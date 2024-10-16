<?php

namespace Database\Factories;

use App\Models\Activeingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActiveIngredientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ActiveIngredient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->unique()->sentence(3),
            "globalname" => $this->faker->unique()->sentence(3)
        ];
    }
}
