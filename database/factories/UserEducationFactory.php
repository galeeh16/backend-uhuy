<?php

namespace Database\Factories;

use App\Models\UserEducation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserEducation>
 */
class UserEducationFactory extends Factory
{
    protected $model = UserEducation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'institution_name' => fake()->company(),
            'degree' => fake()->randomElement(['SMA', 'D3', 'S1']),
            'field_of_study' => fake()->jobTitle(),
            'start_date' => fake()->dateTimeBetween('-10 years', '-5 years')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween('-4 years', 'now')->format('Y-m-d'),
            'grade' => fake()->randomFloat(2, 2.5, 4.0),
            'description' => fake()->sentence(),
        ];
    }

    /**
     * State khusus untuk SMA
     */
    public function sma(): static
    {
        return $this->state(fn (array $attributes) => [
            'institution_name' => 'SMA Negeri 1', // Bisa kamu ganti nama aslinya
            'degree' => 'SMA',
            'field_of_study' => 'IPA', // Atau IPS/SMK
            'start_date' => '2010-07-01',
            'end_date' => '2013-06-30',
        ]);
    }

    /**
     * State khusus untuk S1 Teknik Informatika
     */
    public function s1Informatika(): static
    {
        return $this->state(fn (array $attributes) => [
            'institution_name' => 'Universitas Komputer', // Bisa diganti
            'degree' => 'S1',
            'field_of_study' => 'Teknik Informatika',
            'start_date' => '2013-08-01',
            'end_date' => '2017-09-30',
            'grade' => '3.85',
        ]);
    }
}
