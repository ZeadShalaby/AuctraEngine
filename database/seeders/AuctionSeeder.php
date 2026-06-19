<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuctionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id');
        $subCategories = SubCategory::with('category')->get();

        Auction::factory()
            ->count(50)
            ->make()
            ->each(function ($auction) use ($users, $subCategories) {
                
                $sub = $subCategories->random();

                $auction->user_id = $users->random();
                $auction->sub_category_id = $sub->id;
                $auction->category_id = $sub->category_id;
                $auction->save();

                $images = [
                    storage_path('app/public/images/auction.jpg'),
                    storage_path('app/public/images/auction2.jpg'),
                    storage_path('app/public/images/auction3.jpg'),
                ];

                $videos = [
                    storage_path('app/public/videos/video1.mp4'),
                    storage_path('app/public/videos/video2.mp4'),
                ];

                $randomImages = collect($images)
                    ->shuffle()
                    ->take(rand(1, 2));

                $randomVideos = collect($videos)
                    ->shuffle()
                    ->take(rand(0, 2));

                foreach ($randomImages as $image) {
                    if (file_exists($image)) {
                        $auction->addMedia($image)
                            ->preservingOriginal()
                            ->toMediaCollection('images');
                    }
                }

                foreach ($randomVideos as $video) {
                    if (file_exists($video)) {
                        $auction->addMedia($video)
                            ->preservingOriginal()
                            ->toMediaCollection('videos');
                    }
                }
            });
    }
}