<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_logout_without_login(): void 
    {
        $response = $this->postJson('/api/auth/logout');
        $response
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_logout_success(): void
    {
        $login = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret12345'
        ]);
        $login
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $login->json();

        $user = User::where('email', 'galih@example.com')->first();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Successfully logged out']);
    }
}
