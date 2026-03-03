<?php 

namespace Tests\Concerns;

trait LoginTrait
{
    public function login(array $payload): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/api/auth/login', $payload);
    }
}