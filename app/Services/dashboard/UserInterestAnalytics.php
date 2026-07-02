<?php

namespace App\Services\Dashboard;

use App\Models\Interest;
use App\Models\UserInterest;

class UserInterestAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => UserInterest::count(),

            'today' => UserInterest::whereDate('created_at', today())->count(),

            'this_month' => UserInterest::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'users' => UserInterest::distinct('user_id')
                ->count('user_id'),
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

            'this_week' => UserInterest::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            'users_with_interests' => UserInterest::distinct('user_id')
                ->count('user_id'),

            'average_interests_per_user' => round(
                UserInterest::count() /
                max(UserInterest::distinct('user_id')->count('user_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Interests
            |--------------------------------------------------------------------------
            */

            'interests_count' => Interest::count(),

            'used_interests' => UserInterest::distinct('interest_id')
                ->count('interest_id'),

            'average_score' => round(
                UserInterest::avg('score'),
                2
            ),

            'total_score' => UserInterest::sum('score'),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'highest_score' => UserInterest::orderByDesc('score')
                ->first(),

            'most_used_interest' => UserInterest::selectRaw('interest_id, COUNT(*) total')
                ->groupBy('interest_id')
                ->orderByDesc('total')
                ->first(),

            'most_active_user' => UserInterest::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_user_interests' => UserInterest::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly User Interests
     */
    public function monthlyChart()
    {
        return UserInterest::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Interest Distribution
     */
    public function interestChart()
    {
        return UserInterest::selectRaw('interest_id, COUNT(*) total')
            ->groupBy('interest_id')
            ->pluck('total', 'interest_id');
    }

    /**
     * Score Chart
     */
    public function scoreChart()
    {
        return UserInterest::selectRaw('interest_id, AVG(score) average_score')
            ->groupBy('interest_id')
            ->pluck('average_score', 'interest_id');
    }

    /**
     * Top Interests
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
     * Top Users
     */
    public function topUsers()
    {
        return UserInterest::selectRaw('user_id, COUNT(*) total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}