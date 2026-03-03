<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserProfileDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Storage $storage */
        $storage = Storage::disk('public');

        return [
            'id'                    => $this->id,
            'location'              => $this->location,
            'full_address'          => $this->full_address,
            'about_me'              => $this->about_me,
            'phone'                 => $this->phone,
            'photo'                 => $this->photo? $storage->url($this->photo) : null,
            'cv'                    => $this->cv? $storage->url($this->cv) : null,
            'portfolio'             => $this->portfolio? $storage->url($this->portfolio) : null,
            'birth_date'            => $this->birth_date,
            'experience_year'       => $this->experience_year,
            'availability_for_work' => $this->availability_for_work,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
