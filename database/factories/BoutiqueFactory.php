<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoutiqueFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id'     => User::factory(), // default: kaykhla9 user jdid ila ma3tinach wa7d
            'nom'         => $this->faker->company,
            'description' => $this->faker->sentence,
            'telephone'   => '06' . $this->faker->numerify('########'),
            'email'       => $this->faker->safeEmail,
            'adresse'     => $this->faker->streetAddress,
            'emplacement' => 'Bloc ' . $this->faker->randomLetter,
            'actif'       => true,
        ];
    }
}