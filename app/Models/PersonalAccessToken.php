<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    public function newUniqueId(): string
    {
        return (string) Str::ulid();
    }

}
