<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProfileObserver
{
    public function saved($model): void
    {
        // Karena UserProfile dan CompanyProfile punya user_id / company_id
        // Kita ambil ID-nya untuk membersihkan cache User utama
        $userId = $model->user_id ?? $model->company_id;
        Log::info('clear cache profile ' . $userId);

        if ($userId) {
            Cache::forget("user_auth_{$userId}");
            Cache::forget("token_user_{$userId}_" . app()->environment());

            // clear api/auth/me auth data endpoint
            Cache::forget("user_me_{$userId}");
        }
    }

    public function deleted($model): void
    {
        $this->saved($model);
    }
}
