<?php

namespace Tests\Feature\Talent;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class ListAppliedPostTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;    

    public function test_get_list_applied_post_but_not_authenticated(): void
    {
        $response = $this->getJson('/api/talent/applied/posts');

        $response
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_get_list_applied_post_but_not_talent(): void 
    {
        $companyLogin = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);

        $json = $companyLogin->json();

        $response = $this->getJson('/api/talent/applied/posts', [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonStructure(['message']);
    }

    public function test_get_list_applied_post_success(): void 
    {
        $talentLogin = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!',
        ]);

        $json = $talentLogin->json();

        $response = $this->getJson('/api/talent/applied/posts', [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'post_title',
                        'location',
                        'salary',
                        'applied_at',
                        'status',
                        'company' => [
                            'id',
                            'name',
                            'email',
                            'profile' => [
                                'photo'
                            ]
                        ],
                    ]
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ]
            ]);
    }
}
