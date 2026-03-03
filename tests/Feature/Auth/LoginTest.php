<?php 

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_login_but_failed_validation(): void
    {
        $this->login([
            'email' => '',
            'password' => '',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message', 
            'errors' => [
                'email',
                'password'
            ]
        ]);
    }

    public function test_login_email_or_password_invalid(): void 
    {
        $this->login([
            'email' => 'galih@example.com',
            'password' => 'wkwk'
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'email'
            ],
        ]);
    }

    // public function test_login_is_throttled(): void
    // {
    //     for ($i = 0; $i < 5; $i++) {
    //         $this->postJson('/api/auth/login', [
    //             'email' => 'galih@example.com',
    //             'password' => 'wrong-password',
    //         ]);
    //     }

    //     $this->postJson('/api/auth/login', [
    //         'email' => 'galih@example.com',
    //         'password' => 'wrong-password',
    //     ])
    //     ->assertStatus(429)
    //     ->assertJson([
    //         'message' => 'Too many login attempts. Please try again later.'
    //     ]);
    // }

    public function test_login_success(): void 
    {
       $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret12345'
        ])
        ->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'token'
        ]);
    }
}