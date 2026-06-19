<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        return [

            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'likes_count' => fake()->numberBetween(0, 1000),
            'comments_count' => fake()->numberBetween(0, 500),
            'shares_count' => fake()->numberBetween(0, 200),
            'created_at' => now()->subDays(rand(0, 30)),
        ];
    }
}