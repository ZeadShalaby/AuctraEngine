<?php

namespace App\Services\Dashboard;

use App\Models\AuctionPromotion;
use App\Models\PromotionPackage;

class PromotionPackageAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => PromotionPackage::count(),

            'active' => PromotionPackage::where('is_active', true)->count(),

            'inactive' => PromotionPackage::where('is_active', false)->count(),

            'today' => PromotionPackage::whereDate('created_at', today())->count(),
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

            'this_week' => PromotionPackage::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => PromotionPackage::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Types
            |--------------------------------------------------------------------------
            */

            'featured' => PromotionPackage::where('type', 'featured')->count(),

            'promoted' => PromotionPackage::where('type', 'promoted')->count(),

            /*
            |--------------------------------------------------------------------------
            | Prices
            |--------------------------------------------------------------------------
            */

            'average_price' => round(PromotionPackage::avg('price'), 2),

            'highest_price' => PromotionPackage::max('price'),

            'lowest_price' => PromotionPackage::min('price'),

            'average_days' => round(PromotionPackage::avg('days'), 2),

            /*
            |--------------------------------------------------------------------------
            | Usage
            |--------------------------------------------------------------------------
            */

            'used_packages' => AuctionPromotion::distinct('promotion_package_id')
                ->count('promotion_package_id'),

            'total_promotions' => AuctionPromotion::count(),

            'active_promotions' => AuctionPromotion::where('status', 'active')->count(),

            'promotion_revenue' => AuctionPromotion::sum('price'),

            'most_used_package' => AuctionPromotion::selectRaw('promotion_package_id, COUNT(*) total')
                ->groupBy('promotion_package_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_packages' => PromotionPackage::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Packages
     */
    public function monthlyChart()
    {
        return PromotionPackage::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Package Types
     */
    public function typeChart()
    {
        return PromotionPackage::selectRaw('type, COUNT(*) total')
            ->groupBy('type')
            ->pluck('total', 'type');
    }

    /**
     * Package Status
     */
    public function statusChart()
    {
        return PromotionPackage::selectRaw('is_active, COUNT(*) total')
            ->groupBy('is_active')
            ->pluck('total', 'is_active');
    }

    /**
     * Most Used Packages
     */
    public function topPackages()
    {
        return AuctionPromotion::selectRaw('promotion_package_id, COUNT(*) total')
            ->groupBy('promotion_package_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Monthly Promotion Revenue
     */
    public function revenueChart()
    {
        return AuctionPromotion::selectRaw('MONTH(created_at) month, SUM(price) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }
}