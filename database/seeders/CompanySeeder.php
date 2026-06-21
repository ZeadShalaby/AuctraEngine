<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $companies = [
            ['name' => 'Libyana', 'code' => 'LIB', 'email' => 'info@libyana.ly', 'phone' => '0920000001'],
            ['name' => 'Al-Madar', 'code' => 'MAD', 'email' => 'info@almadar.ly', 'phone' => '0910000001'],
            ['name' => 'Vodafone Egypt', 'code' => 'VOD', 'email' => 'info@vodafone.eg', 'phone' => '01000000001'],
            ['name' => 'Etisalat Egypt', 'code' => 'ETI', 'email' => 'info@etisalat.eg', 'phone' => '01100000001'],
        ];

        foreach ($companies as $data) {
            $company = Company::create($data);
            $image = storage_path('app/public/images/company.png');
            if (file_exists($image)) {
                $company->addMedia($image)
                    ->preservingOriginal()
                    ->toMediaCollection('companyLogo');
            }
        }
    }
}