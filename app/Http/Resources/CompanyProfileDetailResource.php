<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CompanyProfileDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Storage $storage */
        $storage = Storage::disk('public');

        return [
            // 'id'             => $this->id,
            'address'        => $this->address,
            'location'       => $this->location,
            'about_company'  => $this->about_company,
            'company_size'   => $this->company_size,
            'founded_in'     => $this->founded_in,
            'photo'          => $this->photo ? $storage->url($this->photo) : null,
            'website_url'    => $this->website_url,
            'facebook_url'   => $this->facebook_url,
            'instagram_url'  => $this->instagram_url,
            'twitter_url'    => $this->twitter_url,
            'linked_in_url'  => $this->linked_in_url,
        ];
    }
}
