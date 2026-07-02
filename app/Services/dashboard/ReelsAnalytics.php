<?php

namespace App\Services\Dashboard;

use App\Models\Reels;

class ReelsAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Reels::count(),

            'today' => Reels::whereDate('created_at', today())->count(),

            'this_month' => Reels::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'views' => Reels::sum('views_count'),
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

            'this_week' => Reels::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Engagement
            |--------------------------------------------------------------------------
            */

            'likes' => Reels::sum('likes_count'),

            'comments' => Reels::sum('comments_count'),

            'shares' => Reels::sum('shares_count'),

            'favorites' => Reels::sum('favorites_count'),

            'completed_views' => Reels::sum('completed_views_count'),

            /*
            |--------------------------------------------------------------------------
            | Average
            |--------------------------------------------------------------------------
            */

            'average_views' => round(Reels::avg('views_count'), 2),

            'average_likes' => round(Reels::avg('likes_count'), 2),

            'average_comments' => round(Reels::avg('comments_count'), 2),

            'average_shares' => round(Reels::avg('shares_count'), 2),

            'average_completed_views' => round(
                Reels::avg('completed_views_count'),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_viewed' => Reels::orderByDesc('views_count')->first(),

            'most_liked' => Reels::orderByDesc('likes_count')->first(),

            'most_shared' => Reels::orderByDesc('shares_count')->first(),

            'most_commented' => Reels::orderByDesc('comments_count')->first(),

            'most_favorited' => Reels::orderByDesc('favorites_count')->first(),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'unique_creators' => Reels::distinct('user_id')
                ->count('user_id'),

            'reels_with_auction' => Reels::whereNotNull('auction_id')
                ->count(),

            'engagement_rate' => round(
                (
                    Reels::sum('likes_count') +
                    Reels::sum('comments_count') +
                    Reels::sum('shares_count') +
                    Reels::sum('favorites_count')
                ) / max(Reels::sum('views_count'), 1) * 100,
                2
            ),

            'completion_rate' => round(
                Reels::sum('completed_views_count') /
                max(Reels::sum('views_count'), 1) * 100,
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_reels' => Reels::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Reels
     */
    public function monthlyChart()
    {
        return Reels::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Monthly Views
     */
    public function viewsChart()
    {
        return Reels::selectRaw('MONTH(created_at) month, SUM(views_count) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Top Reels
     */
    public function topReels()
    {
        return Reels::orderByDesc('views_count')
            ->take(10)
            ->get();
    }

    /**
     * Top Creators
     */
    public function topUsers()
    {
        return Reels::selectRaw('user_id, SUM(views_count) total_views')
            ->groupBy('user_id')
            ->orderByDesc('total_views')
            ->take(10)
            ->get();
    }
}