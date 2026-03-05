<?php

namespace Tests\Feature\Company;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class CreatePostTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_company_create_post_without_login(): void
    {
        $response = $this->postJson('/api/company/posts');

        $response->assertStatus(401);
    }

    public function test_company_create_post_but_failed_validation(): void
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

        $response = $this->postJson('/api/company/posts', [], [
            'Authorization' => 'Bearer ' . $companyToken
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'post_title',
                    'location',
                    'overview',
                    'responsibilities',
                    'requirements',
                    'skills',
                    'experience_year',
                    'employment_type',
                    'level_type'
                ]
            ]);

        // test create but wrong employment_type
        $response = $this->postJson('/api/company/posts', [
            'employment_type' => 'XXXXX',
         ], [
            'Authorization' => 'Bearer ' . $companyToken
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'employment_type',
                ]
            ]);

        // test create but wrong level_type
        $response = $this->postJson('/api/company/posts', [
            'level_type' => 'SUPER_JUNIOR',
         ], [
            'Authorization' => 'Bearer ' . $companyToken
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'level_type',
                ]
            ]);
    }

    public function test_create_post_success(): void
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

        $response = $this->postJson('/api/company/posts', [
            "post_title" =>"Junior Programmer",
            "location" =>"Jakarta",
            "requirements" =>"Lorem ipsum dolor sit amet.",
            "overview" =>"Lorem ipsum dolor sit amet.",
            "responsibilities" =>"Lorem ipsum dolor sit amet.",
            "skills" =>"Lorem ipsum dolor sit amet.",
            "experience_year" =>"2",
            "employment_type" =>"full_time",
            "level_type" =>"junior"
        ], [
            'Authorization' => 'Bearer ' . $companyToken
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data'
            ]);
    }
}
