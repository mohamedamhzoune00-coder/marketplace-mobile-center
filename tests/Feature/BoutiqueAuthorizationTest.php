<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Boutique;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoutiqueAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // test: vendeur A maqadch y3del boutique dyal vendeur B
    public function test_vendeur_ma_yaqdarch_ye3del_boutique_khra()
    {
        $vendeurA = User::factory()->create(['role' => 'vendeur']);
        $vendeurB = User::factory()->create(['role' => 'vendeur']);

        $boutiqueB = Boutique::factory()->create(['user_id' => $vendeurB->id]);

        // n testiw b token dyal vendeur A
        $response = $this->actingAs($vendeurA, 'sanctum')
            ->putJson("/api/boutiques/{$boutiqueB->id}", [
                'nom' => 'Hacked Name',
            ]);
        $response = $this->actingAs($vendeurA, 'sanctum')
            ->putJson("/api/boutiques/{$boutiqueB->id}", [
                'nom' => 'Hacked Name',
            ]);

        

        $response->assertStatus(403);
        $response->assertStatus(403); // khass yrfd
    }

    // test: vendeur y9dr ye3del boutique dyalo howa
    public function test_vendeur_y9dr_ye3del_boutique_dyalo()
    {
        $vendeur = User::factory()->create(['role' => 'vendeur']);
        $boutique = Boutique::factory()->create(['user_id' => $vendeur->id]);

        $response = $this->actingAs($vendeur, 'sanctum')
            ->putJson("/api/boutiques/{$boutique->id}", [
                'nom' => 'Nouveau Nom',
            ]);

        $response->assertStatus(200);
    }

    // test: visiteur maqadch ykhalq boutique
    public function test_visiteur_ma_yaqdarch_ykhalaq_boutique()
    {
        $visiteur = User::factory()->create(['role' => 'visiteur']);

        $response = $this->actingAs($visiteur, 'sanctum')
            ->postJson('/api/boutiques', [
                'nom'         => 'Ma Boutique',
                'telephone'   => '0612345678',
                'adresse'     => 'Adresse test',
                'emplacement' => 'Bloc A',
            ]);

        $response->assertStatus(403);
    }
}
