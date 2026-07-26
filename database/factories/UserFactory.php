<?php

namespace Database\Factories;

use App\Core\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'type' => UserType::Client,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'failed_login_attempts' => 0,
            'mfa_enabled' => false,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function administrator(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => UserType::Administrator,
        ]);
    }

    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => UserType::Staff,
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'locked_at' => now(),
            'lock_reason' => 'Too many failed login attempts',
            'failed_login_attempts' => 5,
        ]);
    }
}
