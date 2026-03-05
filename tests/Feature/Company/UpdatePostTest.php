<?php

namespace Tests\Feature\Company;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class UpdatePostTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_update_post_without_login(): void
    {
        $postId = DB::table('posts')->first()->id;

        $response = $this->putJson('/api/company/posts/'. $postId, []);

        $response->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_update_post_but_user_is_talent(): void
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);
        $json = $loginTalent->json();

        $postId = DB::table('posts')->first()->id;

        $response = $this->putJson('/api/company/posts/'. $postId, [], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonStructure(['message']);
    }

    public function test_update_post_but_failed_validation(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $json = $loginCompany->json();

        $kopnusId = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postId = DB::table('posts')->where('company_id', $kopnusId)->first()->id;

        $response = $this->putJson('/api/company/posts/'. $postId, [], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(422)
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
                    'level_type',
                ]
            ]);
    }

    public function test_update_post_but_failed_validation_employment_type(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $json = $loginCompany->json();

        $kopnusId = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postId = DB::table('posts')->where('company_id', $kopnusId)->first()->id;

        $response = $this->putJson('/api/company/posts/'. $postId, [
            'post_title' => 'post',
            'location' => 'post',
            'overview' => 'post',
            'responsibilities' => 'post',
            'requirements' => 'post',
            'skills' => 'post',
            'experience_year' => '2',
            'employment_type' => 'wrong',
            'level_type' => 'junior',
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'employment_type',
                ]
            ]);
    }

    public function test_update_post_but_failed_validation_level_type(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $json = $loginCompany->json();

        $kopnusId = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postId = DB::table('posts')->where('company_id', $kopnusId)->first()->id;

        $response = $this->putJson('/api/company/posts/'. $postId, [
            'post_title' => 'post',
            'location' => 'post',
            'overview' => 'post',
            'responsibilities' => 'post',
            'requirements' => 'post',
            'skills' => 'post',
            'experience_year' => '1',
            'employment_type' => 'full_time',
            'level_type' => 'wrong',
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'level_type',
                ]
            ]);
    }

    public function test_update_post_but_failed_validation_experience_year(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $json = $loginCompany->json();

        $kopnusId = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postId = DB::table('posts')->where('company_id', $kopnusId)->first()->id;

        $response = $this->putJson('/api/company/posts/'. $postId, [
            'post_title' => 'post',
            'location' => 'post',
            'overview' => 'post',
            'responsibilities' => 'post',
            'requirements' => 'post',
            'skills' => 'post',
            'experience_year' => 'one',
            'employment_type' => 'full_time',
            'level_type' => 'wrong',
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'level_type',
                ]
            ]);
    }

    public function test_update_post_company_but_not_own_post(): void
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $json = $loginCompany->json();

        $kopnusId = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postId = DB::table('posts')->where('company_id', '<>', $kopnusId)->first()->id;

        $response = $this->putJson('/api/company/posts/'. $postId, [
            'post_title' => 'post',
            'location' => 'post',
            'overview' => 'post',
            'responsibilities' => 'post',
            'requirements' => 'post',
            'skills' => 'post',
            'experience_year' => '4',
            'employment_type' => 'full_time',
            'level_type' => 'junior',
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'You do not own this post.']);
    }

    public function test_update_post_success(): void
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        $json = $loginCompany->json();

        $kopnusId = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postId = DB::table('posts')->where('company_id', $kopnusId)->first()->id;

        $response = $this->putJson('/api/company/posts/'. $postId, [
            'post_title' => 'post',
            'location' => 'post',
            'overview' => 'post',
            'responsibilities' => 'post',
            'requirements' => 'post',
            'skills' => 'post',
            'experience_year' => '4',
            'employment_type' => 'full_time',
            'level_type' => 'junior',
        ], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message']);
    }
}
