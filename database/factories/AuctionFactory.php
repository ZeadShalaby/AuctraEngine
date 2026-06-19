<?php

namespace Database\Factories;

use App\Models\Auction;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuctionFactory extends Factory
{
    protected $model = Auction::class;

    public function definition(): array
    {
        $startPrice = $this->faker->numberBetween(100, 10000);
        $status = $this->faker->randomElement(['pending', 'processing', 'active', 'ended', 'cancelled']);

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'winner_id' => $status === 'ended'
                ? (User::inRandomOrder()->first()?->id ?? User::factory())
                : null,
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),

            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->city(),
            'start_price' => $startPrice,
            'min_bid_increment' => $this->faker->randomFloat(2, 1, 100),
            'buy_now_price' => $startPrice + $this->faker->numberBetween(500, 5000),
            'current_price' => $startPrice,
            'start_at' => now()->subDays(rand(1, 5)),
            'end_at' => now()->addDays(rand(1, 10)),
            'condition' => $this->faker->randomElement(['new', 'used']),
            'status' => $status,
            'terms_price' => $this->faker->randomFloat(2, 0, $startPrice),
            'views' => $this->faker->numberBetween(0, 1000),
            'bids_count' => $this->faker->numberBetween(0, 50),
        ];
    }
}