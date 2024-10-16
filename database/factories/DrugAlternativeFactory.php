<?php

namespace Database\Factories;

use App\Models\Drugalternative;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrugAlternativeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DrugAlternative::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "order" =>  1,
        ];
    }
}
