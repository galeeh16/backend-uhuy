<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use StatusCandidate;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostApply>
 */
class PostApplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(), // Akan membuat post baru jika tidak didefinisikan
            'user_id' => User::talent()->factory(), // Akan membuat user baru jika tidak didefinisikan
            'status' => $this->faker->randomElement(StatusCandidate::cases()),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
