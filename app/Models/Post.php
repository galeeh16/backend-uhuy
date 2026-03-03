<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory, HasUuids;
    
    protected $table = 'posts';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = ['id'];

    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id', 'id');
    }

    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class, 
            'post_applies',
            'post_id',
            'user_id'
            )
            ->withPivot('status') // Tanpa ini tidak keambil kolom2 lain (misal kolom status)
            ->withTimestamps();
    }
}
