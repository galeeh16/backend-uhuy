<?php

namespace App\Http\Resources;

use App\Http\Resources\CompanyProfileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'profile' => new CompanyProfileResource($this->whenLoaded('companyProfile')),
        ];
    }
}
