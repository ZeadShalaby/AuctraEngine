<?php

namespace Database\Seeders;

use App\Models\AdPrice;
use Illuminate\Database\Seeder;

class AdPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      AdPrice::factory()->count(6)->create();

    }
}