<?php

namespace Database\Factories;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyProfileFactory extends Factory
{
    protected $model = CompanyProfile::class;

    public function definition()
    {
        return [
            'address' => $this->faker->address,
            'location' => $this->faker->city,
            'about_company' => $this->faker->paragraph,
            'company_size' => $this->faker->numberBetween(5, 1000),
            'founded_in' => $this->faker->date(),
            // 'photo' => 'company/logo.png',
            // 'website_url' => $this->faker->url,
            // 'facebook_url' => $this->faker->url,
            // 'instagram_url' => $this->faker->url,
            // 'twitter_url' => $this->faker->url,
            // 'linked_in_url' => $this->faker->url,
        ];
    }
}
