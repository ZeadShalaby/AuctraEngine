<?php

namespace App\Services\Dashboard;

use App\Models\AuctionPromotion;
use App\Models\PromotionPackage;

class AuctionPromotionAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => AuctionPromotion::count(),

            'active' => AuctionPromotion::where('status', 'active')->count(),

            'pending' => AuctionPromotion::where('status', 'pending')->count(),

            'expired' => AuctionPromotion::where('status', 'expired')->count(),

            'revenue' => AuctionPromotion::sum('price'),
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

            'cancelled' => AuctionPromotion::where('status', 'cancelled')->count(),

            'completed' => AuctionPromotion::where('status', 'completed')->count(),

            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */

            'today' => AuctionPromotion::whereDate('created_at', today())->count(),

            'this_week' => AuctionPromotion::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => AuctionPromotion::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Revenue
            |--------------------------------------------------------------------------
            */

            'today_revenue' => AuctionPromotion::whereDate('created_at', today())
                ->sum('price'),

            'month_revenue' => AuctionPromotion::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('price'),

            'average_price' => round(AuctionPromotion::avg('price'), 2),

            'highest_price' => AuctionPromotion::max('price'),

            /*
            |--------------------------------------------------------------------------
            | Packages
            |--------------------------------------------------------------------------
            */

            'packages_count' => PromotionPackage::count(),

            'active_packages' => PromotionPackage::where('is_active', true)->count(),

            'featured_packages' => PromotionPackage::where('type', 'featured')->count(),

            'promoted_packages' => PromotionPackage::where('type', 'promoted')->count(),

            'most_used_package' => AuctionPromotion::selectRaw('promotion_package_id, COUNT(*) total')
                ->groupBy('promotion_package_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Running Promotions
            |--------------------------------------------------------------------------
            */

            'running_now' => AuctionPromotion::where('starts_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->count(),

            'expired_now' => AuctionPromotion::where('expires_at', '<', now())->count(),

            /*
            |--------------------------------------------------------------------------
            | Performance
            |--------------------------------------------------------------------------
            */

            'active_rate' => round(
                (
                    AuctionPromotion::where('status', 'active')->count() * 100
                ) / max(AuctionPromotion::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_promotions' => AuctionPromotion::latest()
                ->take(10)
                ->get(),

        ]);
    }

    /**
     * Promotions Created Per Month
     */
    public function monthlyChart()
    {
        return AuctionPromotion::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Revenue Per Month
     */
    public function revenueChart()
    {
        return AuctionPromotion::selectRaw('MONTH(created_at) month, SUM(price) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Status Chart
     */
    public function statusChart()
    {
        return AuctionPromotion::selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Package Usage Chart
     */
    public function packageChart()
    {
        return AuctionPromotion::selectRaw('promotion_package_id, COUNT(*) total')
            ->groupBy('promotion_package_id')
            ->pluck('total', 'promotion_package_id');
    }

    /**
     * Top 10 Expensive Promotions
     */
    public function topPromotions()
    {
        return AuctionPromotion::orderByDesc('price')
            ->take(10)
            ->get();
    }
}