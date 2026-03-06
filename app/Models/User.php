<?php

namespace App\Models;

use App\Models\CompanyProfile;
use App\Models\Post;
use App\Models\UserProfile;
use App\Notifications\RegisterUserNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUlids;

    protected $guarded = ['id'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function newUniqueId(): string
    {
        return (string) Str::ulid();
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class, 'company_id', 'id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'company_id', 'id');
    }

    public function appliedPosts(): BelongsToMany
    {
        return $this->belongsToMany(
            Post::class, 
            'post_applies',
            'user_id',
            'post_id'
            )
            ->withPivot('status') // Tanpa ini tidak keambil kolom2 lain (misal kolom status)
            ->withTimestamps();
    }

    /**
     * Override method default untuk mengirim email verifikasi via Queue.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new RegisterUserNotification());
    }
    
    /**
     * Override method bawaan Laravel
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
