<?php

namespace App\Services\Dashboard;

use App\Models\AuctionTerm;

class AuctionTermAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => AuctionTerm::count(),

            'paid_amount' => AuctionTerm::sum('amount'),

            'refunded' => AuctionTerm::where('is_refunded', true)->count(),

            'today' => AuctionTerm::whereDate('created_at', today())->count(),
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

            'this_week' => AuctionTerm::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => AuctionTerm::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Money
            |--------------------------------------------------------------------------
            */

            'total_paid_amount' => AuctionTerm::sum('amount'),

            'today_amount' => AuctionTerm::whereDate('created_at', today())
                ->sum('amount'),

            'month_amount' => AuctionTerm::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),

            'average_amount' => round(AuctionTerm::avg('amount'), 2),

            'highest_amount' => AuctionTerm::max('amount'),

            'lowest_amount' => AuctionTerm::min('amount'),




            /*
            |--------------------------------------------------------------------------
            | Refunds
            |--------------------------------------------------------------------------
            */

            'refund_count' => AuctionTerm::where('is_refunded', true)->count(),

            'refund_amount' => AuctionTerm::where('is_refunded', true)
                ->sum('amount'),

            'not_refunded_count' => AuctionTerm::where('is_refunded', false)
                ->count(),

            'not_refunded_amount' => AuctionTerm::where('is_refunded', false)
                ->sum('amount'),

            /*
            |--------------------------------------------------------------------------
            | Performance
            |--------------------------------------------------------------------------
            */

            'refund_rate' => round(
                (
                    AuctionTerm::where('is_refunded', true)->count() * 100
                ) / max(AuctionTerm::count(), 1),
                2
            ),

            'unique_users_terms_count' => AuctionTerm::distinct('user_id')->count('user_id'),
            'unique_auctions_terms_count' => AuctionTerm::distinct('auction_id')->count('auction_id'),
            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_terms' => AuctionTerm::latest()
                ->take(10)
                ->get(),

            'highest_terms' => AuctionTerm::orderByDesc('amount')
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Terms Count
     */
    public function monthlyChart()
    {
        return AuctionTerm::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Monthly Revenue
     */
    public function revenueChart()
    {
        return AuctionTerm::selectRaw('MONTH(created_at) month, SUM(amount) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Refund Chart
     */
    public function refundChart()
    {
        return AuctionTerm::selectRaw('is_refunded, COUNT(*) total')
            ->groupBy('is_refunded')
            ->pluck('total', 'is_refunded');
    }

    /**
     * Top Paid Terms
     */
    public function topTerms()
    {
        return AuctionTerm::orderByDesc('amount')
            ->take(10)
            ->get();
    }
}