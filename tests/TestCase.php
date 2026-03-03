<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\Concerns\FakeExternalServices;

abstract class TestCase extends BaseTestCase
{
    use FakeExternalServices;
    
    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('login');
        $this->fakeExternalServices();
    }
}
