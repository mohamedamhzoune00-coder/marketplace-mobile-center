<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase; // kol test kaybda b DB khawya o jdida

    // test 1: visiteur y9dr yseg9el (register)
    public function test_visiteur_peut_s_inscrire()
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role'  => 'visiteur', // khass ykon visiteur b default, machi vendeur
        ]);
    }

    // test 2: user y9dr ydkhel b email/password s7a7
    public function test_user_peut_se_connecter()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'user', 'token', 'token_type']);
    }

    // test 3: password ghalt khass yrfd
    public function test_login_yrfd_b_password_ghalt()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }
}