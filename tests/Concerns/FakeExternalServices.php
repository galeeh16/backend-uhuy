<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

trait FakeExternalServices
{
    protected function fakeExternalServices(): void
    {
        Queue::fake();
        Bus::fake();
        Storage::fake('public');
        Http::fake();
        Event::fake();
        Notification::fake();
        Mail::fake();
    }
}
