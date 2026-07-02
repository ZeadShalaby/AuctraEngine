<?php

namespace App\Services\Dashboard;

use App\Models\Ads;
use App\Models\Auction;
use App\Models\Favourite;
use App\Models\Post;
use App\Models\Reels;

class FavouriteAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Favourite::count(),

            'today' => Favourite::whereDate('created_at', today())->count(),

            'this_month' => Favourite::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'users' => Favourite::distinct('user_id')->count('user_id'),
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

            'this_week' => Favourite::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Favourite Types
            |--------------------------------------------------------------------------
            */

            'posts' => Favourite::where('favoriteable_type', Post::class)->count(),

            'reels' => Favourite::where('favoriteable_type', Reels::class)->count(),

            'auctions' => Favourite::where('favoriteable_type', Auction::class)->count(),

            'ads' => Favourite::where('favoriteable_type', Ads::class)->count(),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'unique_posts' => Favourite::where('favoriteable_type', Post::class)
                ->distinct('favoriteable_id')
                ->count('favoriteable_id'),

            'unique_reels' => Favourite::where('favoriteable_type', Reels::class)
                ->distinct('favoriteable_id')
                ->count('favoriteable_id'),

            'unique_auctions' => Favourite::where('favoriteable_type', Auction::class)
                ->distinct('favoriteable_id')
                ->count('favoriteable_id'),

            'average_per_user' => round(
                Favourite::count() /
                max(Favourite::distinct('user_id')->count('user_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_active_user' => Favourite::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            'most_favourited_item' => Favourite::selectRaw('favoriteable_type, favoriteable_id, COUNT(*) total')
                ->groupBy('favoriteable_type', 'favoriteable_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_favourites' => Favourite::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Favourites
     */
    public function monthlyChart()
    {
        return Favourite::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Favourite Types
     */
    public function typeChart()
    {
        return Favourite::selectRaw('favoriteable_type, COUNT(*) total')
            ->groupBy('favoriteable_type')
            ->pluck('total', 'favoriteable_type');
    }

    /**
     * Top Users
     */
    public function topUsers()
    {
        return Favourite::selectRaw('user_id, COUNT(*) total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Favourite Items
     */
    public function topItems()
    {
        return Favourite::selectRaw('favoriteable_type, favoriteable_id, COUNT(*) total')
            ->groupBy('favoriteable_type', 'favoriteable_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}