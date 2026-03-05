<?php 

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Concerns\LoginTrait;
use Tests\TestCase;

class PostTest extends TestCase
{
    use DatabaseTransactions, LoginTrait;

    public function test_get_list_success(): void 
    {
        $response = $this->getJson('/api/posts');
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
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function test_post_by_id_not_login(): void 
    {
        $postId = Post::first()->id;
        
        $response = $this->getJson('/api/posts/' . $postId);
        $response
            ->assertStatus(401)
            ->assertJsonStructure(['message']);
    }

    public function test_post_by_id_success(): void 
    {
        $loginTalent = $this->login([
            'email' => 'galih@example.com',
            'password' => 'Secret123!'
        ]);

        $json = $loginTalent->json();

        $postId = Post::first()->id;

        $response = $this->getJson('/api/posts/' . $postId, [
            'Authorization' => 'Bearer ' . $json['token'],
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'post_title',
                    'location',
                    'overview',
                    'responsibilities',
                    'requirements',
                    'skills',
                    'experience_year',
                    'employment_type',
                    'level_type',
                    'salary',
                    'total_applied',
                    'created_at',
                    'company' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
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
                        ],
                    ],
                ]
            ]);
    }
}