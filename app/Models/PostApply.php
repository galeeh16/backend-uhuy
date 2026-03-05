<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostApply extends Model
{
    protected $table = 'post_applies';

    protected $primaryKey = 'id';

    protected $keyType = 'string';
    
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post(): BelongsTo 
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}