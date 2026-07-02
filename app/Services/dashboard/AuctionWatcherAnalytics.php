<?php

namespace App\Services\Dashboard;

use App\Models\AuctionWatcher;

class AuctionWatcherAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => AuctionWatcher::count(),

            'today' => AuctionWatcher::whereDate('created_at', today())->count(),

            'unique_users' => AuctionWatcher::distinct('user_id')->count('user_id'),

            'unique_auctions' => AuctionWatcher::distinct('auction_id')->count('auction_id'),
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

            'this_week' => AuctionWatcher::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => AuctionWatcher::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'average_watchers_per_auction' => round(
                AuctionWatcher::count() /
                max(AuctionWatcher::distinct('auction_id')->count('auction_id'), 1),
                2
            ),

            'average_watchers_per_user' => round(
                AuctionWatcher::count() /
                max(AuctionWatcher::distinct('user_id')->count('user_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_watched_auction' => AuctionWatcher::selectRaw('auction_id, COUNT(*) total')
                ->groupBy('auction_id')
                ->orderByDesc('total')
                ->first(),

            'most_active_user' => AuctionWatcher::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_watchers' => AuctionWatcher::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Watchers
     */
    public function monthlyChart()
    {
        return AuctionWatcher::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Top Watched Auctions
     */
    public function topAuctions()
    {
        return AuctionWatcher::selectRaw('auction_id, COUNT(*) total')
            ->groupBy('auction_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Active Users
     */
    public function topUsers()
    {
        return AuctionWatcher::selectRaw('user_id, COUNT(*) total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}