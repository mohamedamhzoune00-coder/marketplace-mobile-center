<?php

namespace Database\Factories;

use App\Models\Produit;
use App\Models\Boutique;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProduitFactory extends Factory
{
    protected $model = Produit::class;

    public function definition()
    {
        return [
            'boutique_id' => Boutique::factory(),
            'category_id' => Category::factory(), // category_id maci categorie_id
            'nom' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'prix' => $this->faker->randomFloat(2, 100, 10000),
            'stock' => $this->faker->numberBetween(1, 50),
            'disponible' => true,
        ];
    }
}