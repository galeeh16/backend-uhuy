<?php

namespace Tests\Feature\Talent;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApplyPostTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_apply_without_login(): void 
    {
        $postId = Post::first()->id;

        // try apply without token
        $this->postJson('/api/talent/posts/' . $postId . '/apply', [])
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_user_apply_job_but_user_role_not_talent(): void
    {   
        // login as company
        $loginCompany = $this->postJson('/api/auth/login', [
            'email' => 'kopnus@example.com',
            'password' => 'Secret123!'
        ]);

        $loginCompany
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginCompany->json();
        $companyToken = $json['token'];

        $postId = Post::first()->id;

        // try apply with company token
        $this->postJson('/api/talent/posts/'. $postId .'/apply', [], [
            'Authorization' => 'Bearer ' .  $companyToken
        ])
        ->assertStatus(403)
        ->assertJsonStructure(['message']);
    }

    public function test_user_talent_apply_but_post_not_found(): void 
    {
        // login as talent
        $loginTalent = $this->postJson('/api/auth/login', [
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);

        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();
        $talentToken = $json['token'];

        $postId = (string) Str::uuid();

        // try apply but post not found
        $this->postJson('/api/talent/posts/'.$postId.'/apply', [], [
            'Authorization' => 'Bearer ' .  $talentToken
        ])
        ->assertStatus(404)
        ->assertJsonStructure(['message']);
    }

    public function test_user_talent_apply_but_multiple_apply(): void 
    {
        // login as talent
        $loginTalent = $this->postJson('/api/auth/login', [
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);

        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();
        $talentToken = $json['token'];

        // cari posts yg belum user apply
        $postId = DB::table('posts')
            ->leftJoin('post_applies', 'posts.id', '=', 'post_applies.post_id')
            ->whereNull('post_applies.post_id')
            ->select('posts.*')
            ->first()
            ->id;

        // first apply
        $this->postJson('/api/talent/posts/' . $postId . '/apply', [], [
            'Authorization' => 'Bearer ' .  $talentToken
        ])
        ->assertStatus(201)
        ->assertJsonStructure(['message']);

        // try apply multiple
        $this->postJson('/api/talent/posts/' . $postId . '/apply', [], [
            'Authorization' => 'Bearer ' .  $talentToken
        ])
        ->assertStatus(409)
        ->assertJsonStructure(['message']);
    }

    public function test_user_talent_apply_success(): void 
    {
        // login as talent
        $loginTalent = $this->postJson('/api/auth/login', [
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);

        $loginTalent
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);

        $json = $loginTalent->json();
        $talentToken = $json['token'];

        // cari posts yg belum user apply
        $postId = DB::table('posts')
            ->leftJoin('post_applies', 'posts.id', '=', 'post_applies.post_id')
            ->whereNull('post_applies.post_id')
            ->select('posts.*')
            ->first()
            ->id;

        // try apply multiple
        $this->postJson('/api/talent/posts/' . $postId . '/apply', [], [
            'Authorization' => 'Bearer ' .  $talentToken
        ])
        ->assertStatus(201)
        ->assertJsonStructure(['message']);
    }
}
