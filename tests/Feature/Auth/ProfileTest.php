<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_get_user_profile_but_not_authenticated(): void 
    {
        $response = $this->getJson('/api/auth/me', []);
        $response->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_get_user_profile_talent(): void 
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);
        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();

        $response = $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer ' . $json['token']
        ]);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'user_profile' => [
                        'id',
                        'location',
                        'full_address',
                        'about_me',
                        'phone',
                        'photo',
                        'cv',
                        'portfolio',
                        'birth_date',
                        'experience_year',
                        'availability_for_work',
                        'created_at',
                    ]
                ]
            ]);
    }

    public function test_get_user_profile_company(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $loginCompany
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginCompany->json();

        $response = $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer ' . $json['token']
        ]);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'company_profile' => [
                        'address',
                        'location',
                        'about_company',
                        'company_size',
                        'founded_in',
                        'photo',
                        'website_url',
                        'facebook_url',
                        'instagram_url',
                        'twitter_url',
                        'linked_in_url',
                    ]
                ]
            ]);
    }
}