<?php

namespace App\Services\Dashboard;

use App\Models\Bid;

class BidAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Bid::count(),

            'today' => Bid::whereDate('created_at', today())->count(),

            'auto_bids' => Bid::where('is_auto', true)->count(),

            'total_amount' => Bid::sum('amount'),
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

            'this_week' => Bid::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => Bid::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Amounts
            |--------------------------------------------------------------------------
            */

            'today_amount' => Bid::whereDate('created_at', today())
                ->sum('amount'),

            'month_amount' => Bid::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),

            'average_amount' => round(Bid::avg('amount'), 2),

            'highest_bid' => Bid::max('amount'),

            'lowest_bid' => Bid::min('amount'),

            /*
            |--------------------------------------------------------------------------
            | Auto Bid
            |--------------------------------------------------------------------------
            */

            'manual_bids' => Bid::where('is_auto', false)->count(),

            'auto_bid_amount' => Bid::where('is_auto', true)
                ->sum('amount'),

            'manual_bid_amount' => Bid::where('is_auto', false)
                ->sum('amount'),

            'average_auto_bid_limit' => round(
                Bid::whereNotNull('max_auto_bid')->avg('max_auto_bid'),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'unique_users' => Bid::distinct('user_id')
                ->count('user_id'),

            'unique_auctions' => Bid::distinct('auction_id')
                ->count('auction_id'),

            'average_bids_per_user' => round(
                Bid::count() /
                max(Bid::distinct('user_id')->count('user_id'), 1),
                2
            ),

            'average_bids_per_auction' => round(
                Bid::count() /
                max(Bid::distinct('auction_id')->count('auction_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'highest_bid_record' => Bid::orderByDesc('amount')->first(),

            'most_active_user' => Bid::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            'most_bidded_auction' => Bid::selectRaw('auction_id, COUNT(*) total')
                ->groupBy('auction_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_bids' => Bid::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Bids
     */
    public function monthlyChart()
    {
        return Bid::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Monthly Bid Amount
     */
    public function revenueChart()
    {
        return Bid::selectRaw('MONTH(created_at) month, SUM(amount) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Auto vs Manual
     */
    public function autoBidChart()
    {
        return Bid::selectRaw('is_auto, COUNT(*) total')
            ->groupBy('is_auto')
            ->pluck('total', 'is_auto');
    }

    /**
     * Top 10 Auctions By Bids
     */
    public function topAuctions()
    {
        return Bid::selectRaw('auction_id, COUNT(*) total')
            ->groupBy('auction_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top 10 Users By Bids
     */
    public function topUsers()
    {
        return Bid::selectRaw('user_id, COUNT(*) total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}