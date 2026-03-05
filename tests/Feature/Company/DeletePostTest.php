<?php

namespace Tests\Feature\Company;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class DeletePostTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_company_delete_post_without_login(): void
    {
        $postId = DB::table('posts')->first()->id;

        $response = $this->deleteJson('/api/company/posts/'. $postId);

        $response->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_delete_post_but_user_role_is_talent(): void 
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);

        $json = $loginTalent->json();

        $postId = DB::table('posts')->first()->id;

        $response = $this->deleteJson('/api/company/posts/'. $postId, [], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonStructure(['message']);
    }

    public function test_company_delete_post_but_not_own_post(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);

        $json = $loginCompany->json();

        $kopnusId = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postId = DB::table('posts')->where('company_id', '<>', $kopnusId)->first()->id;

        $response = $this->deleteJson('/api/company/posts/'. $postId, [], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'You are not allowed to perform this action.']);
    }

    public function test_company_delete_post_but_post_not_found(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);

        $json = $loginCompany->json();

        $id = (string) Str::uuid();

        $response = $this->deleteJson('/api/company/posts/'.$id, [], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(404)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Post not found']);
    }

    public function test_company_delete_post_success(): void 
    {
        $loginCompany = $this->login([
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);
        
        $json = $loginCompany->json();

        $kopnusId = DB::table('users')->where('email', 'kopnus@example.com')->first()->id;
        $postId = DB::table('posts')->where('company_id', $kopnusId)->first()->id;

        $response = $this->deleteJson('/api/company/posts/'. $postId, [], [
            'Authorization' => 'Bearer ' . $json['token']
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Success delete post']);
    }
    
}
