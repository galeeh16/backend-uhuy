<?php 

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_register_but_failed_validation(): void 
    {
        $this->postJson('/api/auth/register', [
            'email' => '',
            'name' => '',
            'password' => '',
            'password_confirmation' => '',
            'role' => '',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'email',
                'name',
                'password',
                'password_confirmation',
                'role'
            ]
        ]);
    }

    public function test_register_but_email_exists(): void 
    {
        $this->postJson('/api/auth/register', [
            'email' => 'galih@example.com',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'email',
            ]
        ]);
    }

    public function test_register_but_weak_password(): void 
    {
        // password only one character
        $this->postJson('/api/auth/register', [
            'password' => '1',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'password',
            ]
        ]);

        // password only 8 character letter
        $this->postJson('/api/auth/register', [
            'password' => 'password',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'password',
            ]
        ]);

        // password only 8 character letter and number
        $this->postJson('/api/auth/register', [
            'password' => 'password123',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'password',
            ]
        ]);

        // password only 8 character letter and number and symbols
        $this->postJson('/api/auth/register', [
            'password' => 'password123!',
        ])
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'password',
            ]
        ]);
    }

    public function test_register_talent_success(): void 
    {
        $this->postJson('/api/auth/register', [
            'email' => rand() . 'talent@example.com',
            'name' => 'Unit Talent test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'TALENT',
        ])
        ->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'email',
                'name',
                'role'
            ]
        ]);
    }

    public function test_register_company_success(): void 
    {
        $this->postJson('/api/auth/register', [
            'email' => rand() . 'company@example.com',
            'name' => 'Unit Company test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'COMPANY',
        ])
        ->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'email',
                'name',
                'role'
            ]
        ]);
    }
}