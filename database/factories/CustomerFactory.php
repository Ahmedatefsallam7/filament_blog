<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genders = ['male' => 'male', 'female' => 'female'];
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->email(),
            'photo' => fake()->imageUrl(),
            'gender' => array_rand($genders),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'birthday' => fake()->dateTimeThisDecade(),
        ];
    }
}
