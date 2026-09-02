<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BoutiqueFactory extends Factory
{
    public function definition()
    {
        return [
            'nom'         => $this->faker->company,
            'description' => $this->faker->sentence,
            'telephone'   => '06' . $this->faker->numerify('########'),
            'email'       => $this->faker->safeEmail,
            'adresse'     => $this->faker->streetAddress,
            'emplacement' => 'Bloc ' . $this->faker->randomLetter,
            'actif'       => true,
            // user_id ma7tinach lina, khass yjib mn 3nd li kaystaamel Factory
        ];
    }
}
