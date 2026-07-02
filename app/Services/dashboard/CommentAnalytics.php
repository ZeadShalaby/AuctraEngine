<?php

namespace App\Services\Dashboard;

use App\Models\Comment;

class CommentAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Comment::count(),

            'today' => Comment::whereDate('created_at', today())->count(),

            'this_month' => Comment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'users' => Comment::distinct('user_id')->count('user_id'),
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

            'this_week' => Comment::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Comment Types
            |--------------------------------------------------------------------------
            */

            'posts_comments' => Comment::where('commentable_type', 'App\Models\Post')->count(),

            'reels_comments' => Comment::where('commentable_type', 'App\Models\Reels')->count(),

            'ads_comments' => Comment::where('commentable_type', 'App\Models\Ads')->count(),

            'auctions_comments' => Comment::where('commentable_type', 'App\Models\Auction')->count(),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'unique_posts' => Comment::where('commentable_type', 'App\Models\Post')
                ->distinct('commentable_id')
                ->count('commentable_id'),

            'unique_reels' => Comment::where('commentable_type', 'App\Models\Reels')
                ->distinct('commentable_id')
                ->count('commentable_id'),

            'average_comments_per_user' => round(
                Comment::count() /
                max(Comment::distinct('user_id')->count('user_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_active_user' => Comment::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            'most_commented_post' => Comment::where('commentable_type', 'App\Models\Post')
                ->selectRaw('commentable_id, COUNT(*) total')
                ->groupBy('commentable_id')
                ->orderByDesc('total')
                ->first(),

            'most_commented_reel' => Comment::where('commentable_type', 'App\Models\Reels')
                ->selectRaw('commentable_id, COUNT(*) total')
                ->groupBy('commentable_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_comments' => Comment::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Comments
     */
    public function monthlyChart()
    {
        return Comment::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Commentable Types
     */
    public function typeChart()
    {
        return Comment::selectRaw('commentable_type, COUNT(*) total')
            ->groupBy('commentable_type')
            ->pluck('total', 'commentable_type');
    }

    /**
     * Top Users
     */
    public function topUsers()
    {
        return Comment::selectRaw('user_id, COUNT(*) total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Commented Items
     */
    public function topItems()
    {
        return Comment::selectRaw('commentable_type, commentable_id, COUNT(*) total')
            ->groupBy('commentable_type', 'commentable_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}