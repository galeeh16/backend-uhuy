<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyProfile extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'company_profile';

    protected $guarded = ['id'];

    protected $keyType = 'string';

    public $incrementing = false;

    public function newUniqueId(): string
    {
        return (string) Str::uuid();
    }
}
