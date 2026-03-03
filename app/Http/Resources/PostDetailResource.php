<?php

namespace App\Http\Resources;

use App\Http\Resources\CompanyDetailResource;
use App\Http\Resources\CompanyProfileDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PostDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'post_title'        => $this->post_title,
            'location'          => $this->location,
            'overview'          => $this->overview,
            'responsibilities'  => $this->responsibilities,
            'requirements'      => $this->requirements,
            'skills'            => $this->skills,
            'experience_year'   => $this->experience_year,
            'employment_type'   => $this->employment_type,
            'level_type'        => $this->level_type,
            'salary'            => $this->salary,
            'total_applied'     => $this->total_applied,
            'created_at'        => $this->created_at,
            'company'           => new CompanyDetailResource($this->whenLoaded('company')),

            // 'company' => $this->whenLoaded('company', function () {
            //     return [
            //         'id'   => $this->company->id,
            //         'name' => $this->company->name,
            //         'profile' => new CompanyProfileDetailResource($this->company->companyProfile),
            //     ];
            // }),
        ];
    }
}
