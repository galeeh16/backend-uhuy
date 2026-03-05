<?php

namespace Tests\Feature\Company;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class ShowPostTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_show_post_without_login(): void
    {
        $response = $this->getJson('/api/company/posts/1');

        $response->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_show_post_login_as_talent(): void 
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);

        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();
        $talentToken = $json['token'];

        $idKopnus = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postNotKopnusId = DB::table('posts')->where('company_id', '<>', $idKopnus)->first()->id;

        $response = $this->getJson('/api/company/posts/'.$postNotKopnusId, [
            'Authorization' => 'Bearer ' . $talentToken
        ]);

        $response->assertStatus(403)
            ->assertJsonStructure(['message']);
    }

    public function test_show_post_as_company_but_not_own_post(): void 
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

        $idKopnus = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postNotKopnusId = DB::table('posts')->where('company_id', '<>', $idKopnus)->first()->id;

        $response = $this->getJson('/api/company/posts/'.$postNotKopnusId, [
            'Authorization' => 'Bearer ' . $companyToken
        ]);

        $response->assertStatus(403)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'You are not allowed to perform this action.']);
    }

    public function test_show_post_as_company_success(): void 
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

        $idKopnus = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postKopnusId = DB::table('posts')->where('company_id', $idKopnus)->first()->id;

        $response = $this->getJson('/api/company/posts/'.$postKopnusId, [
            'Authorization' => 'Bearer ' . $companyToken
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'post_title',
                    'location',
                    'overview',
                    'responsibilities',
                    'requirements',
                    'experience_year',
                    'employment_type',
                    'level_type',
                    'salary',
                    'total_applied',
                    'created_at',
                    'company' => [
                        'id',
                        'name',
                        'profile' => [
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
                ]
            ]);
    }
}
