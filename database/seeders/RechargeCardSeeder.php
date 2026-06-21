<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\RechargeCard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RechargeCardSeeder extends Seeder
{
    public function run()
    {
        $cards = Card::all();

        foreach ($cards as $card) {
            for ($j = 1; $j <= 5; $j++) {
                RechargeCard::create([
                    'card_id' => $card->id,
                    'card_number' => fake()->numerify('################'),
                    'used' => false,
                    'recharge_amount' => $card->recharge_amount
                ]);
            }
        }
    }
}