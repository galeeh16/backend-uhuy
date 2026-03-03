<?php 

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TalentAppliedPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'post_title'  => $this->post_title,
            'location'    => $this->location,
            'salary'      => $this->salary,
            'applied_at'  => $this->pivot->created_at,
            'status'      => $this->pivot->status,
            'company'     => new CompanyResource($this->whenLoaded('company')),
        ];
    }
}
