<?php

namespace Tests\Feature\Company;

use App\Models\PostApply;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class CandidateTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_list_candidate_but_not_authenticated(): void
    {
        $response = $this->getJson('/api/company/candidates');

        $response->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_list_candidate_but_not_company(): void 
    {
        // login as talent
        $companyLogin = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);

        $json = $companyLogin->json();

        // hit api list candidates
        $response = $this->getJson('/api/company/candidates', [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response->assertStatus(403)
            ->assertJsonStructure(['message']);
    }

    public function test_list_candidate_success(): void 
    {
        // login as company
        $companyLogin = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);

        $json = $companyLogin->json();

        // hit api list candidates
        $response = $this->getJson('/api/company/candidates', [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message', 
                'data' => [
                    '*' => [
                        'id',
                        'candidate_id',
                        'candidate_name',
                        'candidate_email',
                        'candidate_photo',
                        'post_title',
                        'status',
                        'applied_at',
                    ]
                ], 
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    // show candidate apply
    public function test_show_candidate_apply_but_not_login(): void 
    {
        $postApplyId = DB::table('post_applies')->first()->id;

        $response = $this->getJson('/api/company/candidates/' . $postApplyId);

        $response->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_show_candidate_success(): void 
    {
        // login as company kopnus
        $companyLogin = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);

        $json = $companyLogin->json();

        $postApplyId = PostApply::first()->id;

        $response = $this->getJson('/api/company/candidates/' . $postApplyId, [
            'Authorization' => 'Bearer ' . $json['token'],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data'
            ]);
    }
}
