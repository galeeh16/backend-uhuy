<?php

namespace Database\Factories;

use App\Models\UserWorkExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserWorkExperience>
 */
class UserWorkExperienceFactory extends Factory
{
    protected $model = UserWorkExperience::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'position' => fake()->jobTitle(),
            'location' => fake()->city(),
            'start_date' => fake()->dateTimeBetween('-5 years', '-2 years')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
            'description' => fake()->paragraph(),
        ];
    }

    /**
     * State khusus untuk pengalaman di Bank Mandiri (2017 - 2018)
     */
    public function mandiri(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_name' => 'Bank Mandiri',
            'position' => 'IT Support', // Sesuaikan dengan posisi aslimu
            'start_date' => '2017-01-01',
            'end_date' => '2018-12-31',
        ]);
    }

    /**
     * State khusus untuk pengalaman di Inarts (2018 - 2019)
     */
    public function inarts(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_name' => 'Inarts',
            'position' => 'Web Developer', 
            'start_date' => '2018-01-01',
            'end_date' => '2019-12-31',
        ]);
    }

    /**
     * State khusus untuk pengalaman di Kopnus (2019 - Sekarang)
     */
    public function kopnus(): static
    {
        return $this->state(fn (array $attributes) => [
            'company_name' => 'Kopnus',
            'position' => 'Backend Developer', // Sesuaikan posisi
            'start_date' => '2019-01-01',
            'end_date' => null, // Dikosongkan karena masih bekerja
        ]);
    }
}
