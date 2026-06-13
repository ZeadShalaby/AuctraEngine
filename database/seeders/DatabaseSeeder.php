<?php

namespace Database\Seeders;

use App\Models\Ads;
use App\Models\Category;
use App\Models\Post;
use App\Models\Reels;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionTableSeeder::class,
            RoleTableSeeder::class,
            UserTableSeeder::class,
        ]);

        User::factory(40)->create();

        // ================= POSTS =================
        Post::factory(100)->create()->each(function ($post) {

            $image = storage_path('app/public/images/back2.png');
            $video = storage_path('app/public/videos/video1.mp4');

            if (file_exists($image)) {
                $post->addMedia($image)
                    ->preservingOriginal()
                    ->toMediaCollection('image');
            }

            if (file_exists($video)) {
                $post->addMedia($video)
                    ->preservingOriginal()
                    ->toMediaCollection('video');
            }
        });

        // ================= REELS =================
        Reels::factory(60)->create()->each(function ($reel) {

            $video = storage_path('app/public/videos/video1.mp4');
            $thumb = storage_path('app/public/images/back2.png');

            if (file_exists($video)) {
                $reel->addMedia($video)
                    ->preservingOriginal()
                    ->toMediaCollection('video');
            }

            if (file_exists($thumb)) {
                $reel->addMedia($thumb)
                    ->preservingOriginal()
                    ->toMediaCollection('thumbnail');
            }
        });

        // ================= ADS =================
        Ads::factory(25)->create()->each(function ($ad) {

            $image = storage_path('app/public/images/back2.png');
            $video = storage_path('app/public/videos/video1.mp4');

            if (file_exists($image)) {
                $ad->addMedia($image)
                    ->preservingOriginal()
                    ->toMediaCollection('image');
            }

            if (file_exists($video)) {
                $ad->addMedia($video)
                    ->preservingOriginal()
                    ->toMediaCollection('video');
            }
        });

        // ================= CATEGORIES =================
        $categories = [
            ['name_en' => 'Watches', 'name_ar' => 'ساعات'],
            ['name_en' => 'Cars', 'name_ar' => 'سيارات'],
            ['name_en' => 'Mechanics', 'name_ar' => 'لوحات فنية'],
            ['name_en' => 'Jewelry', 'name_ar' => 'خردة'],
            
        ];
        foreach ($categories as $category) {
            Category::factory()->create(['name_en' => $category['name_en'], 'name_ar' => $category['name_ar']]);
        }
    }
}