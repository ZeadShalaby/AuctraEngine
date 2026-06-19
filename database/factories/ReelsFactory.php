<?php

namespace Database\Factories;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReelsFactory extends Factory
{
    public function definition(): array
    {
        return [

            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'auction_id' => fake()->boolean(50)
                ? Auction::inRandomOrder()->first()?->id
                : null,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'views_count' => fake()->numberBetween(1000, 500000),
            'likes_count' => fake()->numberBetween(0, 50000),
            'comments_count' => fake()->numberBetween(0, 10000),
            'shares_count' => fake()->numberBetween(0, 5000),
            'completed_views_count' => fake()->numberBetween(0, 100000),
            'created_at' => now()->subDays(rand(0, 30)),
        ];
    }
}