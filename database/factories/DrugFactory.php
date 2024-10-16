<?php

namespace Database\Factories;

use App\Models\Drug;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrugFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Drug::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "name" => $this->faker->sentence(3),
            "brandname" => $this->faker->sentence(3),
            "type" => 1,
            "description" => $this->faker->text(20),
            "barcode" => $this->faker->unique()->isbn13(),
            "middleunitnum" => $this->faker->numberBetween(1, 100),
            "smallunitnum" => $this->faker->numberBetween(1, 100),
            "visible" => 1,
            "created_at" => now()
        ];
    }
}
