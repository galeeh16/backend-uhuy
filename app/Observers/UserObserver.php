<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function clearUserCache(User $user): void
    {
        $cacheKey = "user_auth_{$user->id}"; // $identifier di EloquentCachedUserProvider.php

        Log::info('forget user cache' . $cacheKey);

        Cache::forget($cacheKey);
        // Jika kamu juga meng-cache tokenable di PersonalAccessToken model:
        Cache::forget("token_user_{$user->id}_" . app()->environment());
    }

    public function updated(User $user): void
    {
        $this->clearUserCache($user);
    }

    public function deleted(User $user): void
    {
        $this->clearUserCache($user);
    }
}
