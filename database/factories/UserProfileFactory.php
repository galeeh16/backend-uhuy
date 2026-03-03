<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition()
    {
        return [
            'location' => $this->faker->city,
            'full_address' => $this->faker->address,
            'about_me' => $this->faker->paragraph,
            'phone' => $this->faker->phoneNumber,
            'photo' => 'photos/default.png',
            'cv' => 'cv/default.pdf',
            'portfolio' => $this->faker->url,
            'birth_date' => $this->faker->date(),
            'experience_year' => $this->faker->numberBetween(0, 15),
            'availability_for_work' => true,
        ];
    }
}
