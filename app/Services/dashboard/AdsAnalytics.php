<?php

namespace App\Services\Dashboard;

use App\Models\Ads;
use App\Models\Category;
use App\Models\Favourite;
use App\Models\SubCategory;

class AdsAnalytics
{
    public function dashboard(): array
    {
        return [
            'total' => Ads::count(),

            'active' => Ads::where('status', 'active')->count(),

            'live' => Ads::where('status', 'live')->count(),

            'pending' => Ads::where('status', 'pending')->count(),

            'today' => Ads::whereDate('created_at', today())->count(),
        ];
    }

    public function full(): array
    {
        return array_merge($this->dashboard(), [

            'rejected' => Ads::where('status', 'rejected')->count(),

            'review' => Ads::where('status', 'review')->count(),

            'expired' => Ads::where('status', 'expired')->count(),

            'this_week' => Ads::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),

            'this_month' => Ads::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'total_impressions' => Ads::sum('current_impressions'),

            'max_impressions' => Ads::sum('max_impressions'),

            'average_impressions' => round(Ads::avg('current_impressions'), 2),

            'reels_ads' => Ads::where('feed_type', 'reels')->count(),

            'posts_ads' => Ads::where('feed_type', 'posts')->count(),

            'both_ads' => Ads::where('feed_type', 'both')->count(),

            'top_ad' => Ads::orderByDesc('current_impressions')->first(),

            'running_percentage' => round(
                (Ads::sum('current_impressions') * 100) /
                max(Ads::sum('max_impressions'), 1),
                2
            ),
        ]);
    }

    /**
     * Chart : Ads Created Per Month
     */
    public function monthlyChart()
    {
        return Ads::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Chart : Ads Per Category
     */
    public function statusChart()
    {
        return Ads::selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    public function feedTypeChart()
    {
        return Ads::selectRaw('feed_type, COUNT(*) total')
            ->groupBy('feed_type')
            ->pluck('total', 'feed_type');
    }

    /**
     * Latest Ads
     */
    public function latest(int $limit = 10)
    {
        return Ads::latest()
            ->take($limit)
            ->get();
    }
}