<?php

namespace App\Http\Resources;

use App\Http\Resources\CompanyProfileDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'profile' => new CompanyProfileDetailResource($this->whenLoaded('companyProfile')),
        ];
    }
}
