<?php

namespace Database\Factories;

use App\Models\AdPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdPrice>
 */
class AdPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'placement' => $this->faker->randomElement(['feed', 'reels', 'posts', 'both']),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'is_active' => $this->faker->boolean(),
            'max_impressions' => $this->faker->randomNumber(5),
            'max_days' => $this->faker->randomNumber(2),
        ];
    }
}
