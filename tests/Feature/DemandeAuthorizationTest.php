<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Boutique;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DemandeAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_visiteur_peut_creer_demande()
    {
        $user = User::factory()->create([
            'role' => 'visiteur',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/demandes', [
            'produit_id' => 1,
            'nom_client' => 'Test Client',
            'telephone' => '0600000000',
            'quantite' => 1,
        ]);

        $this->assertNotEquals(403, $response->status());
    }

    public function test_vendeur_ne_peut_pas_creer_demande()
    {
        $user = User::factory()->create([
            'role' => 'vendeur',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/demandes', [
            'produit_id' => 1,
            'nom_client' => 'Test Client',
            'telephone' => '0600000000',
            'quantite' => 1,
        ]);

        $response->assertForbidden();
    }
}