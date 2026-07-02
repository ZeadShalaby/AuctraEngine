<?php

namespace App\Services\Dashboard;

use App\Models\Review;

class ReviewAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Review::count(),

            'today' => Review::whereDate('created_at', today())->count(),

            'this_month' => Review::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'average_rating' => round(Review::avg('rating'), 2),
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

            'this_week' => Review::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Ratings
            |--------------------------------------------------------------------------
            */

            'five_stars' => Review::where('rating', 5)->count(),

            'four_stars' => Review::where('rating', 4)->count(),

            'three_stars' => Review::where('rating', 3)->count(),

            'two_stars' => Review::where('rating', 2)->count(),

            'one_star' => Review::where('rating', 1)->count(),

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            'reviewers' => Review::distinct('reviewer_id')
                ->count('reviewer_id'),

            'reviewed_sellers' => Review::distinct('seller_id')
                ->count('seller_id'),

            'average_reviews_per_seller' => round(
                Review::count() /
                max(Review::distinct('seller_id')->count('seller_id'), 1),
                2
            ),

            'average_reviews_per_reviewer' => round(
                Review::count() /
                max(Review::distinct('reviewer_id')->count('reviewer_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Auctions
            |--------------------------------------------------------------------------
            */

            'reviewed_auctions' => Review::distinct('auction_id')
                ->count('auction_id'),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'top_reviewer' => Review::selectRaw('reviewer_id, COUNT(*) total')
                ->groupBy('reviewer_id')
                ->orderByDesc('total')
                ->first(),

            'top_rated_seller' => Review::selectRaw('seller_id, AVG(rating) average_rating')
                ->groupBy('seller_id')
                ->orderByDesc('average_rating')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_reviews' => Review::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Reviews
     */
    public function monthlyChart()
    {
        return Review::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Rating Distribution
     */
    public function ratingChart()
    {
        return Review::selectRaw('rating, COUNT(*) total')
            ->groupBy('rating')
            ->pluck('total', 'rating');
    }

    /**
     * Top Reviewers
     */
    public function topReviewers()
    {
        return Review::selectRaw('reviewer_id, COUNT(*) total')
            ->groupBy('reviewer_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Rated Sellers
     */
    public function topRatedSellers()
    {
        return Review::selectRaw('seller_id, AVG(rating) average_rating')
            ->groupBy('seller_id')
            ->orderByDesc('average_rating')
            ->take(10)
            ->get();
    }
}