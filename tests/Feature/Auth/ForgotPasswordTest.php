<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use DatabaseTransactions;    

    // send reset link
    public function test_send_reset_link_failed_validation(): void
    {
        $response = $this->postJson('/api/auth/forgot-password/send-reset-link', []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_send_reset_link_email_invalid(): void
    {
        $response = $this->postJson('/api/auth/forgot-password/send-reset-link', [
            'email' => 'galih@invalid.com'
        ]);

        $response
            ->assertStatus(400)
            ->assertJsonStructure(['message']);
    }

    public function test_send_reset_link_success(): void
    {
        $response = $this->postJson('/api/auth/forgot-password/send-reset-link', [
            'email' => 'galih@example.com'
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message']);
    }


    // forgot password
    public function test_reset_password_but_failed_validation(): void 
    {
        $response = $this->postJson('/api/auth/forgot-password/reset-password', [
            'token' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'token',
                    'email',
                    'password',
                    'password_confirmation'
                ],
            ]);
    }

    public function test_reset_password_but_wrong_token(): void 
    {
        $response = $this->postJson('/api/auth/forgot-password/reset-password', [
            'token' => 'dsadsadsadadsa',
            'email' => 'galih@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
        ]);

        $response
            ->assertStatus(400)
            ->assertJsonStructure(['message']);
    }

    public function test_reset_password_success(): void 
    {
        $this->postJson('/api/auth/forgot-password/send-reset-link', [
            'email' => 'galih@example.com'
        ]);

        $token = DB::table('password_reset_tokens')->where('email', 'galih@example.com')->first()->token;

        $response = $this->postJson('/api/auth/forgot-password/reset-password', [
            'token' => $token,
            'email' => 'galih@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
        ]);

        $response
            ->assertStatus(400)
            ->assertJsonStructure(['message']);
    }
}
