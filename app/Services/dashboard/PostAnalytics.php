<?php

namespace App\Services\Dashboard;

use App\Models\Post;

class PostAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Post::count(),

            'today' => Post::whereDate('created_at', today())->count(),

            'this_month' => Post::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'total_likes' => Post::sum('likes_count'),

            'total_comments' => Post::sum('comments_count'),

            'total_shares' => Post::sum('shares_count'),
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

            'this_week' => Post::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Engagement
            |--------------------------------------------------------------------------
            */

            'average_likes_per_post' => round(
                Post::avg('likes_count'),
                2
            ),

            'average_comments_per_post' => round(
                Post::avg('comments_count'),
                2
            ),

            'average_shares_per_post' => round(
                Post::avg('shares_count'),
                2
            ),

            'average_engagement_per_post' => round(
                Post::selectRaw('AVG(likes_count + comments_count + shares_count) as engagement')
                    ->value('engagement'),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Posts
            |--------------------------------------------------------------------------
            */

            'posts_without_likes' => Post::where('likes_count', 0)->count(),

            'posts_without_comments' => Post::where('comments_count', 0)->count(),

            'posts_without_shares' => Post::where('shares_count', 0)->count(),

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            'active_authors' => Post::distinct('user_id')
                ->count('user_id'),

            'average_posts_per_user' => round(
                Post::count() /
                max(Post::distinct('user_id')->count('user_id'), 1),
                2
            ),

            'top_author' => Post::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_liked_post' => Post::orderByDesc('likes_count')->first(),

            'most_commented_post' => Post::orderByDesc('comments_count')->first(),

            'most_shared_post' => Post::orderByDesc('shares_count')->first(),

            'most_engaged_post' => Post::selectRaw('*, (likes_count + comments_count + shares_count) as engagement')
                ->orderByDesc('engagement')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_posts' => Post::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Posts
     */
    public function monthlyChart()
    {
        return Post::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Monthly Engagement
     */
    public function monthlyEngagementChart()
    {
        return Post::selectRaw("
                MONTH(created_at) month,
                SUM(likes_count) likes,
                SUM(comments_count) comments,
                SUM(shares_count) shares
            ")
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->get();
    }

    /**
     * Top Liked Posts
     */
    public function topLikedPosts()
    {
        return Post::orderByDesc('likes_count')
            ->take(10)
            ->get();
    }

    /**
     * Top Commented Posts
     */
    public function topCommentedPosts()
    {
        return Post::orderByDesc('comments_count')
            ->take(10)
            ->get();
    }

    /**
     * Top Shared Posts
     */
    public function topSharedPosts()
    {
        return Post::orderByDesc('shares_count')
            ->take(10)
            ->get();
    }

    /**
     * Top Engaged Posts
     */
    public function topEngagedPosts()
    {
        return Post::selectRaw('*, (likes_count + comments_count + shares_count) as engagement')
            ->orderByDesc('engagement')
            ->take(10)
            ->get();
    }

    /**
     * Top Authors
     */
    public function topAuthors()
    {
        return Post::selectRaw('user_id, COUNT(*) total_posts')
            ->groupBy('user_id')
            ->orderByDesc('total_posts')
            ->take(10)
            ->get();
    }
}