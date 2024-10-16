<?php

namespace Database\Factories;

use App\Models\Contraindication;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContraindicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contraindication::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "category" => 1,
            "description" => $this->faker->text(20),
            "level" => 1,
            "order"=>1
        ];
    }
}
