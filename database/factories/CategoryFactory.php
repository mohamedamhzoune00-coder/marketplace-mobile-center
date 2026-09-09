<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition()
    {
        return [
            'nom'         => $this->faker->word,
            'description' => $this->faker->sentence,
            'ordre'       => 0,
            'actif'       => true,
        ];
    }
}