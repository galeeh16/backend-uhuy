<?php

namespace App\Http\Resources;

use App\Http\Resources\CompanyProfileDetailResource;
use App\Http\Resources\UserProfileDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'role'  => $this->role,

            'user_profile' => new UserProfileDetailResource($this->whenLoaded('userProfile')),

            'company_profile' => new CompanyProfileDetailResource($this->whenLoaded('companyProfile'))
        ];
    }
}
