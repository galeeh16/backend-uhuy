<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Storage $storage */
        $storage = Storage::disk('public');

        return [
            'id'         => $this->id,
            'phone'      => $this->phone,
            'address'    => $this->address,
            'location'   => $this->location,
            // 'bio'        => $this->bio,
            'photo'      => $this->photo? $storage->url($this->photo) : null,
        ];
    }
}
