<?php

namespace App\Services\Dashboard;

use App\Models\Ads;
use App\Models\Auction;
use App\Models\Post;
use App\Models\Reels;
use App\Models\reports\Report;
use App\Models\User;

class ReportAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Report::count(),

            'pending' => Report::where('status', 'pending')->count(),

            'resolved' => Report::where('status', 'resolved')->count(),

            'today' => Report::whereDate('created_at', today())->count(),
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

            'reviewed' => Report::where('status', 'reviewed')->count(),

            'rejected' => Report::where('status', 'rejected')->count(),

            'resolution_rate' => round(
                Report::where('status', 'resolved')->count() * 100 /
                max(Report::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'this_week' => Report::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => Report::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Report Types
            |--------------------------------------------------------------------------
            */

            'users' => Report::where('reportable_type', User::class)->count(),

            'posts' => Report::where('reportable_type', Post::class)->count(),

            'reels' => Report::where('reportable_type', Reels::class)->count(),

            'auctions' => Report::where('reportable_type', Auction::class)->count(),

            'ads' => Report::where('reportable_type', Ads::class)->count(),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'unique_reporters' => Report::distinct('user_id')
                ->count('user_id'),

            'handled_reports' => Report::whereNotNull('admin_id')->count(),

            'handled_rate' => round(
                Report::whereNotNull('admin_id')->count() * 100 /
                max(Report::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_active_reporter' => Report::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            'most_reported_item' => Report::selectRaw('reportable_type, reportable_id, COUNT(*) total')
                ->groupBy('reportable_type', 'reportable_id')
                ->orderByDesc('total')
                ->first(),

            'top_admin' => Report::selectRaw('admin_id, COUNT(*) total')
                ->whereNotNull('admin_id')
                ->groupBy('admin_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_reports' => Report::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Reports
     */
    public function monthlyChart()
    {
        return Report::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Status Chart
     */
    public function statusChart()
    {
        return Report::selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Report Types Chart
     */
    public function typeChart()
    {
        return Report::selectRaw('reportable_type, COUNT(*) total')
            ->groupBy('reportable_type')
            ->pluck('total', 'reportable_type');
    }

    /**
     * Top Reporters
     */
    public function topUsers()
    {
        return Report::selectRaw('user_id, COUNT(*) total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Reported Items
     */
    public function topItems()
    {
        return Report::selectRaw('reportable_type, reportable_id, COUNT(*) total')
            ->groupBy('reportable_type', 'reportable_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}