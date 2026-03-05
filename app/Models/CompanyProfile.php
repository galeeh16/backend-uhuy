<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CompanyProfile extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'company_profile';

    protected $guarded = ['id'];

    protected $keyType = 'string';

    public $incrementing = false;

    public function newUniqueId(): string
    {
        return (string) Str::ulid();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id', 'id');
    }
}
