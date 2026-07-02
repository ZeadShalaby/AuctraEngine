<?php

namespace App\Services\Dashboard;

use App\Models\Category;
use App\Models\Auction;
use App\Models\SubCategory;

class CategoryAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Category::count(),

            'today' => Category::whereDate('created_at', today())->count(),

            'this_month' => Category::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
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

            'this_week' => Category::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */

            'sub_categories' => SubCategory::count(),

            'average_sub_categories' => round(
                SubCategory::count() / max(Category::count(), 1),
                2
            ),

            'auctions' => Auction::count(),

            'average_auctions_per_category' => round(
                Auction::count() / max(Category::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top Categories
            |--------------------------------------------------------------------------
            */

            'most_used_category' => Auction::selectRaw('category_id, COUNT(*) total')
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_categories' => Category::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Categories Per Month
     */
    public function monthlyChart()
    {
        return Category::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Auctions Per Category
     */
    public function auctionChart()
    {
        return Auction::selectRaw('category_id, COUNT(*) total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');
    }

    /**
     * Top Categories
     */
    public function topCategories()
    {
        return Auction::selectRaw('category_id, COUNT(*) total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}