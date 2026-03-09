<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Cache;

class EloquentCachedUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        // Gunakan key unik berdasarkan ID user
        $cacheKey = "user_auth_{$identifier}";

        info('cache ' . $cacheKey);

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($identifier) {
            return parent::retrieveById($identifier);
        });
    }
}
