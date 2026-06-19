<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PromotionPackage>
 */
class PromotionPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => $this->faker->randomElement(['featured', 'promoted']),
            'days' => $this->faker->numberBetween(1, 30),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
