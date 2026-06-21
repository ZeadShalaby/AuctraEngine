<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Card;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{

    public function run()
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            for ($i = 1; $i <= 2; $i++) {
                Card::create([
                    'company_id' => $company->id,
                    'name' => "{$company->name} Pack $i",
                    'selling_price' => 50.00,
                    'amount' => 50.00,
                    'recharge_amount' => 50.00,
                ]);
            }
        }
    }
}