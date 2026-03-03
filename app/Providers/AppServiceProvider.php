<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isProduction = app()->isProduction();

        // if (!$isProduction) {
        //     DB::listen(function(QueryExecuted $queryExecuted) {
        //         Log::debug('query executed', [
        //             'sql' => $queryExecuted->toRawSql(),
        //             'time' => $queryExecuted->time
        //         ]);
        //     });
        // }

        Model::preventLazyLoading(!$isProduction);

        $this->configurePolicy();

        $this->configureRateLimiting();

        $this->configureVerifikasiEmail();
    }

    private function configurePolicy(): void
    {
        Gate::policy(Post::class, PostPolicy::class);

        Gate::policy(User::class, UserPolicy::class);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(strtolower($request->input('email')).'|'.$request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many login attempts. Please try again later.'
                    ], 429);
                });
        });
    }

    private function configureVerifikasiEmail(): void 
    {
        VerifyEmail::createUrlUsing(function ($notifiable) {
            $id = $notifiable->getKey();
            $hash = sha1($notifiable->getEmailForVerification());
            
            // Buat signed URL versi API
            $rawUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $id, 'hash' => $hash]
            );

            $frontend_url_verify_email = config('app.frontend_url') . config('app.frontend_url_verify_email');

            // Arahkan ke Nuxt, lalu Nuxt yang akan panggil URL di atas via fetch
            return "{$frontend_url_verify_email}?verify_url=" . urlencode($rawUrl);
        });
    }
}
