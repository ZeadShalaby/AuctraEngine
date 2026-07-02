<?php

namespace App\Services\Dashboard;

use App\Models\Ads;
use App\Models\Auction;
use App\Models\Post;
use App\Models\Reels;
use App\Models\Share;

class ShareAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Share::count(),

            'today' => Share::whereDate('created_at', today())->count(),

            'this_month' => Share::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'users' => Share::distinct('user_id')->count('user_id'),
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

            'this_week' => Share::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Share Types
            |--------------------------------------------------------------------------
            */

            'posts' => Share::where('shareable_type', Post::class)->count(),

            'reels' => Share::where('shareable_type', Reels::class)->count(),

            'auctions' => Share::where('shareable_type', Auction::class)->count(),

            'ads' => Share::where('shareable_type', Ads::class)->count(),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'unique_posts' => Share::where('shareable_type', Post::class)
                ->distinct('shareable_id')
                ->count('shareable_id'),

            'unique_reels' => Share::where('shareable_type', Reels::class)
                ->distinct('shareable_id')
                ->count('shareable_id'),

            'unique_auctions' => Share::where('shareable_type', Auction::class)
                ->distinct('shareable_id')
                ->count('shareable_id'),

            'unique_ads' => Share::where('shareable_type', Ads::class)
                ->distinct('shareable_id')
                ->count('shareable_id'),

            'average_per_user' => round(
                Share::count() /
                max(Share::distinct('user_id')->count('user_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_active_user' => Share::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            'most_shared_item' => Share::selectRaw('shareable_type, shareable_id, COUNT(*) total')
                ->groupBy('shareable_type', 'shareable_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_shares' => Share::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Shares
     */
    public function monthlyChart()
    {
        return Share::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Share Types
     */
    public function typeChart()
    {
        return Share::selectRaw('shareable_type, COUNT(*) total')
            ->groupBy('shareable_type')
            ->pluck('total', 'shareable_type');
    }

    /**
     * Top Users
     */
    public function topUsers()
    {
        return Share::selectRaw('user_id, COUNT(*) total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Shared Items
     */
    public function topItems()
    {
        return Share::selectRaw('shareable_type, shareable_id, COUNT(*) total')
            ->groupBy('shareable_type', 'shareable_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}