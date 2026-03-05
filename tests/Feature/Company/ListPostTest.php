<?php

namespace Tests\Feature\Company;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\LoginTrait;

class ListPostTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_get_list_but_not_authenticated(): void
    {
        $response = $this->getJson('/api/company/posts');

        $response->assertStatus(401);
    }

    public function test_get_list_success(): void
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);

        $loginCompany
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginCompany->json();
        $companyToken = $json['token'];

        $response = $this->getJson('/api/company/posts', [
            'Authorization' => 'Bearer ' . $companyToken
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'items' => [
                        '*' => [
                            'id',
                            'post_title',
                            'location',
                            'experience_year',
                            'employment_type',
                            'level_type',
                            'created_at',
                            'company' => [
                                'id',
                                'name',
                                'email',
                                'profile' => [
                                    'photo'
                                ]
                            ]
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                    ]
                ]
            ]);
    }
}
