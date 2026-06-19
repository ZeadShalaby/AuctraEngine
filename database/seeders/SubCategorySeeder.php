<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        $data = [
            'ساعات' => [
                ['ar' => 'ساعات يد', 'en' => 'Wristwatches'],
                ['ar' => 'ساعات حائط', 'en' => 'Wall clocks'],
                ['ar' => 'ساعات رقمية', 'en' => 'Digital watches'],
                ['ar' => 'ساعات زيتية', 'en' => 'Oil watches'],
            ],
            'سيارات' => [
                ['ar' => 'سيدان', 'en' => 'Sedan'],
                ['ar' => 'SUV', 'en' => 'SUV'],
                ['ar' => 'هاتشباك', 'en' => 'Hatchback'],
                ['ar' => 'نقل', 'en' => 'Trucks'],
                ['ar' => 'كابرات', 'en' => 'Cabs'],
                ['ar' => 'مرسيدس', 'en' => 'Mercedes'],
                ['ar' => 'لاندروس', 'en' => 'Landros'],
            ],
            'لوحات فنية' => [
                ['ar' => 'لوحات زيتية', 'en' => 'Oil paintings'],
                ['ar' => 'تصوير فوتوغرافي', 'en' => 'Photography'],
                ['ar' => 'نحت', 'en' => 'Sculpture'],
                ['ar' => 'ديكورات', 'en' => 'Decorations'],
            ],
            'خردة' => [
                ['ar' => 'قطع غيار', 'en' => 'Spare parts'],
                ['ar' => 'معادن', 'en' => 'Metals'],
                ['ar' => 'أدوات قديمة', 'en' => 'Antique tools'],
                ['ar' => 'صناعة', 'en' => 'Crafts'],
            ],
        ];

        foreach ($data as $categoryNameAr => $subCategories) {
            $category = Category::where('name_ar', $categoryNameAr)->first();

            if ($category) {
                foreach ($subCategories as $sub) {
                    SubCategory::create([
                        'category_id' => $category->id,
                        'name_ar'     => $sub['ar'],
                        'name_en'     => $sub['en'],
                        'slug'        => Str::slug($sub['en']), 
                    ]);
                }
            }
        }
    }
}
