<?php

namespace App\Services\Dashboard;

use App\Models\Ads;
use App\Models\Auction;
use App\Models\Like;
use App\Models\Post;
use App\Models\Reels;

class LikeAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Like::count(),

            'today' => Like::whereDate('created_at', today())->count(),

            'this_month' => Like::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'users' => Like::distinct('user_id')->count('user_id'),
        ];
    }

    /**
     * Full Analytics
     */
    public function full(): array
    {
        return array_merge($this->dashboard(), [

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'this_week' => Like::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Like Types
            |--------------------------------------------------------------------------
            */

            'posts' => Like::where('likeable_type', Post::class)->count(),

            'reels' => Like::where('likeable_type', Reels::class)->count(),

            'auctions' => Like::where('likeable_type', Auction::class)->count(),

            'ads' => Like::where('likeable_type', Ads::class)->count(),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'unique_posts' => Like::where('likeable_type', Post::class)
                ->distinct('likeable_id')
                ->count('likeable_id'),

            'unique_reels' => Like::where('likeable_type', Reels::class)
                ->distinct('likeable_id')
                ->count('likeable_id'),

            'unique_auctions' => Like::where('likeable_type', Auction::class)
                ->distinct('likeable_id')
                ->count('likeable_id'),

            'unique_ads' => Like::where('likeable_type', Ads::class)
                ->distinct('likeable_id')
                ->count('likeable_id'),

            'average_per_user' => round(
                Like::count() /
                max(Like::distinct('user_id')->count('user_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_active_user' => Like::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            'most_liked_item' => Like::selectRaw('likeable_type, likeable_id, COUNT(*) total')
                ->groupBy('likeable_type', 'likeable_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_likes' => Like::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Likes
     */
    public function monthlyChart()
    {
        return Like::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Like Types Chart
     */
    public function typeChart()
    {
        return Like::selectRaw('likeable_type, COUNT(*) total')
            ->groupBy('likeable_type')
            ->pluck('total', 'likeable_type');
    }

    /**
     * Top Users
     */
    public function topUsers()
    {
        return Like::selectRaw('user_id, COUNT(*) total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Liked Items
     */
    public function topItems()
    {
        return Like::selectRaw('likeable_type, likeable_id, COUNT(*) total')
            ->groupBy('likeable_type', 'likeable_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}