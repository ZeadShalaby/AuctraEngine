<?php

namespace App\Providers;

use App\Contracts\WhatsAppServiceInterface;
use App\Models\Ads;
use App\Models\Auction;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Complaint;
use App\Models\Interest;
use App\Models\Post;
use App\Models\ReelInterest;
use App\Models\Reels;
use App\Models\reports\Report;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(AuthRepositoryInterface::class,AuthRepository::class);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();
        //
        Relation::morphMap([
            'setting'       => Setting::class,
            'user'          => User::class,
            'post'          => Post::class,
            'reel'          => Reels::class,
            'reel_interest' => ReelInterest::class,
            'interest'      => Interest::class,
            'ads'           => Ads::class,
            'category'      => Category::class,
            'Auction'       => Auction::class,
            'report'        => Report::class,
            'review'        => Review::class,
            'comment'       => Comment::class,
            'complaint'     => Complaint::class,
        ]);
    }
}
