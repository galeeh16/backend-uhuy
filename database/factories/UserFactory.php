<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Secret123!'),
            'role' => UserRole::Talent->value, // default
            'remember_token' => Str::random(10),
        ];
    }

    public function talent()
    {
        return $this->state(fn () => [
            'role' => UserRole::Talent->value
        ]);
    }

    public function company()
    {
        return $this->state(fn () => [
            'role' => UserRole::Company->value
        ]);
    }

    public function admin()
    {
        return $this->state(fn () => [
            'role' => UserRole::Admin->value
        ]);
    }

    public function unverified()
    {
        return $this->state(fn () => [
            'email_verified_at' => null
        ]);
    }
}
