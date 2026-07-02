<?php

namespace App\Services\Dashboard;

use App\Models\Category;
use App\Models\SubCategory;

class SubCategoryAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => SubCategory::count(),

            'today' => SubCategory::whereDate('created_at', today())->count(),

            'this_month' => SubCategory::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'categories' => SubCategory::distinct('category_id')
                ->count('category_id'),
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

            'this_week' => SubCategory::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Categories
            |--------------------------------------------------------------------------
            */

            'categories_count' => Category::count(),

            'average_subcategories_per_category' => round(
                SubCategory::count() /
                max(Category::count(), 1),
                2
            ),

            'empty_categories' => Category::whereDoesntHave('subCategories')->count(),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'category_with_most_subcategories' => SubCategory::selectRaw('category_id, COUNT(*) total')
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_subcategories' => SubCategory::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly SubCategories
     */
    public function monthlyChart()
    {
        return SubCategory::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Categories Chart
     */
    public function categoryChart()
    {
        return SubCategory::selectRaw('category_id, COUNT(*) total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');
    }

    /**
     * Top Categories
     */
    public function topCategories()
    {
        return SubCategory::selectRaw('category_id, COUNT(*) total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}