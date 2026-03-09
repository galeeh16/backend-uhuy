<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserEducation extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'user_educations';

    protected $guarded = ['id'];

    protected $keyType = 'string';

    public function newUniqueId(): string
    {
        return (string) Str::ulid();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
