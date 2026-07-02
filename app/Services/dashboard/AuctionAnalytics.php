<?php

namespace App\Services\Dashboard;

use App\Models\Auction;
use App\Models\AuctionPromotion;
use App\Models\AuctionTerm;
use App\Models\AuctionWatcher;

class AuctionAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Auction::count(),

            'today' => Auction::whereDate('created_at', today())->count(),

            'active' => Auction::where('status', 'active')->count(),

            'processing' => Auction::where('status', 'processing')->count(),

            'pending' => Auction::where('status', 'pending')->count(),

            'live' => Auction::where('status', 'live')->count(),
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
            | Status
            |--------------------------------------------------------------------------
            */

            'ended' => Auction::where('status', 'ended')->count(),

            'rejected' => Auction::where('status', 'rejected')->count(),

            'cancelled' => Auction::where('status', 'cancelled')->count(),

            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */

            'this_week' => Auction::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),

            'this_month' => Auction::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Prices
            |--------------------------------------------------------------------------
            */

            'total_start_price' => Auction::sum('start_price'),

            'total_current_price' => Auction::sum('current_price'),

            'average_start_price' => round(Auction::avg('start_price'), 2),

            'average_current_price' => round(Auction::avg('current_price'), 2),

            'highest_current_price' => Auction::max('current_price'),

            'highest_buy_now_price' => Auction::max('buy_now_price'),

            /*
            |--------------------------------------------------------------------------
            | Buy Now
            |--------------------------------------------------------------------------
            */

            'buy_now_enabled' => Auction::whereNotNull('buy_now_price')->count(),

            /*
            |--------------------------------------------------------------------------
            | Views
            |--------------------------------------------------------------------------
            */

            'total_views' => Auction::sum('views'),

            'average_views' => round(Auction::avg('views'), 2),

            'most_viewed' => Auction::orderByDesc('views')->first(),

            /*
            |--------------------------------------------------------------------------
            | Bids
            |--------------------------------------------------------------------------
            */

            'total_bids' => Auction::sum('bids_count'),

            'average_bids' => round(Auction::avg('bids_count'), 2),

            'most_bids' => Auction::orderByDesc('bids_count')->first(),

            /*
            |--------------------------------------------------------------------------
            | Winners
            |--------------------------------------------------------------------------
            */

            'finished_with_winner' => Auction::whereNotNull('winner_id')->count(),

            /*
            |--------------------------------------------------------------------------
            | Condition
            |--------------------------------------------------------------------------
            */

            'new_items' => Auction::where('condition', 'new')->count(),

            'used_items' => Auction::where('condition', 'used')->count(),

            /*
            |--------------------------------------------------------------------------
            | Auction Terms
            |--------------------------------------------------------------------------
            */

            'terms_paid_count' => AuctionTerm::count(),

            'terms_amount' => AuctionTerm::sum('amount'),

            'terms_refunded' => AuctionTerm::where('is_refunded', true)->count(),

            'terms_refunded_amount' => AuctionTerm::where('is_refunded', true)->sum('amount'),

            /*
            |--------------------------------------------------------------------------
            | Watchers
            |--------------------------------------------------------------------------
            */

            'watchers' => AuctionWatcher::count(),

            'average_watchers' => round(
                AuctionWatcher::count() / max(Auction::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Promotions
            |--------------------------------------------------------------------------
            */

            'promoted_auctions' => AuctionPromotion::count(),

            'active_promotions' => AuctionPromotion::where('status', 'active')->count(),

            'pending_promotions' => AuctionPromotion::where('status', 'pending')->count(),

            'expired_promotions' => AuctionPromotion::where('status', 'expired')->count(),

            'promotion_revenue' => AuctionPromotion::sum('price'),

            /*
            |--------------------------------------------------------------------------
            | Ratios
            |--------------------------------------------------------------------------
            */

            'auction_success_rate' => round(
                (Auction::whereNotNull('winner_id')->count() * 100)
                / max(Auction::where('status', 'ended')->count(), 1),
                2
            ),

            'view_per_bid' => round(
                Auction::sum('views')
                / max(Auction::sum('bids_count'), 1),
                2
            ),

        ]);
    }

    /**
     * Auctions Created Per Month
     */
    public function monthlyChart()
    {
        return Auction::selectRaw("
                MONTH(created_at) as month,
                COUNT(*) as total
            ")
            ->whereYear('created_at', now()->year)
            ->groupByRaw("MONTH(created_at)")
            ->pluck('total', 'month');
    }

    /**
     * Status Chart
     */
    public function statusChart()
    {
        return Auction::selectRaw("
                status,
                COUNT(*) as total
            ")
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Condition Chart
     */
    public function conditionChart()
    {
        return Auction::selectRaw("
                `condition`,
                COUNT(*) as total
            ")
            ->groupBy('condition')
            ->pluck('total', 'condition');
    }

    /**
     * Top 10 Viewed Auctions
     */
    public function topViewed()
    {
        return Auction::orderByDesc('views')
            ->take(10)
            ->get();
    }

    /**
     * Top 10 Bids
     */
    public function topBids()
    {
        return Auction::orderByDesc('bids_count')
            ->take(10)
            ->get();
    }

    /**
     * Highest Current Price
     */
    public function topPrices()
    {
        return Auction::orderByDesc('current_price')
            ->take(10)
            ->get();
    }
}