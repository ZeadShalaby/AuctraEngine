<?php

namespace App\Providers;

use App\Models\Ads;
use App\Models\Auction;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Complaint;
use App\Models\Favourite;
use App\Models\Interest;
use App\Models\Post;
use App\Models\ReelInterest;
use App\Models\Reels;
use App\Models\reports\Report;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\Eloquent\AdsRepository;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\CommentRepository;
use App\Repositories\Eloquent\ComplaintRepository;
use App\Repositories\Eloquent\FavouritesRepository;
use App\Repositories\Eloquent\InterestsRepository;
use App\Repositories\Eloquent\LikesRepository;
use App\Repositories\Eloquent\NotificationsRepository;
use App\Repositories\Eloquent\PostsRepository;
use App\Repositories\Eloquent\ReelsRepository;
use App\Repositories\Eloquent\ReportRepository;
use App\Repositories\Eloquent\ReviewsRepository;
use App\Repositories\Eloquent\SharesRepository;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\ComplaintRepositoryInterface;
use App\Repositories\Interfaces\FavouritesRepositoryInterface;
use App\Repositories\Interfaces\InterestsRepositoryInterface;
use App\Repositories\Interfaces\LikesRepositoryInterface;
use App\Repositories\Interfaces\NotificationsRepositoryInterface;
use App\Repositories\Interfaces\PostsRepositoryInterface;
use App\Repositories\Interfaces\ReelsRepositoryInterface;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Repositories\Interfaces\ReviewsRepositoryInterface;
use App\Repositories\Interfaces\SharesRepositoryInterface;
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
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(InterestsRepositoryInterface::class, InterestsRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(ComplaintRepositoryInterface::class, ComplaintRepository::class);
        $this->app->bind(AdsRepositoryInterface::class, AdsRepository::class);
        $this->app->bind(NotificationsRepositoryInterface::class, NotificationsRepository::class);
        $this->app->bind(PostsRepositoryInterface::class, PostsRepository::class);
        $this->app->bind(ReelsRepositoryInterface::class, ReelsRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(LikesRepositoryInterface::class, LikesRepository::class);
        $this->app->bind(FavouritesRepositoryInterface::class, FavouritesRepository::class);
        $this->app->bind(SharesRepositoryInterface::class, SharesRepository::class);
        $this->app->bind(ReviewsRepositoryInterface::class, ReviewsRepository::class);
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
            'setting' => Setting::class,
            'user' => User::class,
            'post' => Post::class,
            'reel' => Reels::class,
            'reel_interest' => ReelInterest::class,
            'interest' => Interest::class,
            'ads' => Ads::class,
            'category' => Category::class,
            'Auction' => Auction::class,
            'report' => Report::class,
            'review' => Review::class,
            'comment' => Comment::class,
            'complaint' => Complaint::class,
            'favourite' => Favourite::class
        ]);
    }
}
