<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => ucfirst($this->faker->firstName()), // <-- Изменить/Добавить
            'last_name' => ucfirst($this->faker->lastName()),   // <-- Изменить/Добавить
            'patronymic' => ucfirst($this->faker->middleName()), // <-- Добавить (faker может не иметь middleName, можно использовать lastName или кастомную логику)
            // Для русского отчества можно сделать так:
            // 'patronymic' => $this->faker->randomElement(['Иванович', 'Петрович', 'Александрович', 'Сергеевна', 'Андреевна', 'Михайловна']),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'), // Используйте хеширование
            'birth_date' => $this->faker->date(), // <-- Добавить
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
