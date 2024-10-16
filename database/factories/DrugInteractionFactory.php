<?php

namespace Database\Factories;

use App\Models\Druginteraction;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrugInteractionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DrugInteraction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "level" => 1,
            "description" => $this->faker->text(20)
        ];
    }
}
