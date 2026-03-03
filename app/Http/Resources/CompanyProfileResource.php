<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CompanyProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Storage $storage */
        $storage = Storage::disk('public');

        return [
            'photo' => $this->photo ? $storage->url($this->photo) : null
        ];
    }
}
