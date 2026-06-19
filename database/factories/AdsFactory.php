<?php

namespace Database\Factories;

use App\Models\AdPrice;
use App\Models\Ads;
use App\Models\Auction;
use App\Models\Post;
use App\Models\Reels;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdsFactory extends Factory
{
    protected $model = Ads::class;

    public function definition(): array
    {
        $type = fake()->randomElement([Post::class, Reels::class]);

        $model = $type::inRandomOrder()->first() ?? null;

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'auction_id' => fake()->optional(0.7)->passthrough(
                Auction::inRandomOrder()->first()?->id
            ) ?? null,
            'ad_price_id' => AdPrice::inRandomOrder()->first()?->id ?? AdPrice::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'adable_id' => $model->id ?? null,
            'adable_type' => $type,
            'status' => 'active',
            'starts_at' => now()->subDays(rand(1, 5)),
            'expires_at' => now()->addDays(rand(5, 30)),
            'max_impressions' => fake()->numberBetween(1000, 100000),
            'current_impressions' => fake()->numberBetween(0, 500),
            'link_url' => fake()->url(),
        ];
    }
}