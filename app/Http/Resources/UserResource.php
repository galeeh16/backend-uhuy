<?php

namespace App\Http\Resources;

use App\Http\Resources\CompanyProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,

            'profile' => $this->whenLoaded('userProfile',
                fn () => new UserProfileResource($this->userProfile)
            ),

            'company_profile' => $this->whenLoaded('companyProfile',
                fn () => new CompanyProfileResource($this->companyProfile)
            ),
        ];
    }
}
