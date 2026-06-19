<?php

namespace Database\Seeders;

use App\Models\PromotionPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            ['name' => 'Basic Featured', 'type' => 'featured', 'days' => 3, 'price' => 50.00, 'is_active' => true],
            ['name' => 'Premium Featured', 'type' => 'featured', 'days' => 7, 'price' => 100.00, 'is_active' => true],
            ['name' => 'Basic Promoted', 'type' => 'promoted', 'days' => 3, 'price' => 30.00, 'is_active' => true],
            ['name' => 'Gold Promoted', 'type' => 'promoted', 'days' => 14, 'price' => 150.00, 'is_active' => true],
            ['name' => 'VIP Pro', 'type' => 'featured', 'days' => 30, 'price' => 300.00, 'is_active' => true],
        ];

        foreach ($packages as $package) {
            PromotionPackage::create($package);
        }
    }
}
