<?php

namespace App\Services\Dashboard;

use App\Models\Category;
use App\Models\Interest;
use App\Models\UserInterest;

class InterestAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Interest::count(),

            'today' => Interest::whereDate('created_at', today())->count(),

            'this_month' => Interest::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'categories' => Interest::distinct('category_id')->count('category_id'),
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

            'this_week' => Interest::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Categories
            |--------------------------------------------------------------------------
            */

            'categories_count' => Category::count(),

            'average_interests_per_category' => round(
                Interest::count() /
                max(Category::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            'user_interests' => UserInterest::count(),

            'users_with_interests' => UserInterest::distinct('user_id')
                ->count('user_id'),

            'average_interests_per_user' => round(
                UserInterest::count() /
                max(UserInterest::distinct('user_id')->count('user_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_used_interest' => UserInterest::selectRaw('interest_id, COUNT(*) total')
                ->groupBy('interest_id')
                ->orderByDesc('total')
                ->first(),

            'most_used_category' => Interest::selectRaw('category_id, COUNT(*) total')
                ->groupBy('category_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_interests' => Interest::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Interests
     */
    public function monthlyChart()
    {
        return Interest::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Interests Per Category
     */
    public function categoryChart()
    {
        return Interest::selectRaw('category_id, COUNT(*) total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');
    }

    /**
     * Most Used Interests
     */
    public function topInterests()
    {
        return UserInterest::selectRaw('interest_id, COUNT(*) total')
            ->groupBy('interest_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Categories
     */
    public function topCategories()
    {
        return Interest::selectRaw('category_id, COUNT(*) total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}