<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'post_title'        => $this->post_title,
            'location'          => $this->location,
            'experience_year'   => $this->experience_year,
            'employment_type'   => $this->employment_type,
            'level_type'        => $this->level_type,
            'created_at'        => $this->created_at,

            'company'           => new CompanyResource($this->whenLoaded('company'))
        ];
    }
}
