<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Models\UserProfile;

class UserAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => User::count(),

            'today' => User::whereDate('created_at', today())->count(),

            'this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'active' => User::where('status', 'active')->count(),
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

            'pending' => User::where('status', 'pending')->count(),

            'blocked' => User::where('status', 'blocked')->count(),

            'inactive' => User::where('status', 'inactive')->count(),

            /*
            |--------------------------------------------------------------------------
            | Verification
            |--------------------------------------------------------------------------
            */

            'verified_email' => User::whereNotNull('email_verified_at')->count(),

            'unverified_email' => User::whereNull('email_verified_at')->count(),

            /*
            |--------------------------------------------------------------------------
            | User Types
            |--------------------------------------------------------------------------
            */

            'normal_users' => User::where('user_type', 'user')->count(),

            'admins' => User::where('user_type', 'admin')->count(),

            /*
            |--------------------------------------------------------------------------
            | Notifications
            |--------------------------------------------------------------------------
            */

            'notifications_enabled' => User::where('notifications_enabled', true)->count(),

            'email_enabled' => User::where('email_enabled', true)->count(),

            'ads_enabled' => User::where('ads_enabled', true)->count(),

            'auction_enabled' => User::where('auction_enabled', true)->count(),

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'this_week' => User::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Profile
            |--------------------------------------------------------------------------
            */

            'profiles' => UserProfile::count(),

            'users_without_profile' => User::doesntHave('profile')->count(),

            'profile_completion_rate' => round(
                UserProfile::count() * 100 / max(User::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Countries
            |--------------------------------------------------------------------------
            */

            'countries' => UserProfile::whereNotNull('country')
                ->distinct('country')
                ->count('country'),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_users' => User::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Users
     */
    public function monthlyChart()
    {
        return User::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * User Status Chart
     */
    public function statusChart()
    {
        return User::selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * User Types Chart
     */
    public function typeChart()
    {
        return User::selectRaw('user_type, COUNT(*) total')
            ->groupBy('user_type')
            ->pluck('total', 'user_type');
    }

    /**
     * Countries Chart
     */
    public function countriesChart()
    {
        return UserProfile::selectRaw('country, COUNT(*) total')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('total')
            ->pluck('total', 'country');
    }

    /**
     * Top Cities
     */
    public function citiesChart()
    {
        return UserProfile::selectRaw('city, COUNT(*) total')
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('total')
            ->take(10)
            ->pluck('total', 'city');
    }
}