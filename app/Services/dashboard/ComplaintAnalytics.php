<?php

namespace App\Services\Dashboard;

use App\Models\Auction;
use App\Models\Complaint;
use App\Models\Post;
use App\Models\Reels;
use App\Models\User;

class ComplaintAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Complaint::count(),

            'pending' => Complaint::where('status', 'pending')->count(),

            'resolved' => Complaint::where('status', 'resolved')->count(),

            'today' => Complaint::whereDate('created_at', today())->count(),
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

            'reviewed' => Complaint::where('status', 'reviewed')->count(),

            'rejected' => Complaint::where('status', 'rejected')->count(),

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'this_week' => Complaint::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => Complaint::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Complaint Types
            |--------------------------------------------------------------------------
            */

            'users_complaints' => Complaint::where('complaintable_type', User::class)->count(),

            'posts_complaints' => Complaint::where('complaintable_type', Post::class)->count(),

            'reels_complaints' => Complaint::where('complaintable_type', Reels::class)->count(),

            'auctions_complaints' => Complaint::where('complaintable_type', Auction::class)->count(),

            /*
            |--------------------------------------------------------------------------
            | Statistics
            |--------------------------------------------------------------------------
            */

            'unique_users' => Complaint::distinct('user_id')->count('user_id'),

            'resolution_rate' => round(
                (
                    Complaint::where('status', 'resolved')->count() * 100
                ) / max(Complaint::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_active_user' => Complaint::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            'most_reported_item' => Complaint::selectRaw('complaintable_type, complaintable_id, COUNT(*) total')
                ->groupBy('complaintable_type', 'complaintable_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_complaints' => Complaint::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Complaints
     */
    public function monthlyChart()
    {
        return Complaint::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Status Chart
     */
    public function statusChart()
    {
        return Complaint::selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Complaint Types Chart
     */
    public function typeChart()
    {
        return Complaint::selectRaw('complaintable_type, COUNT(*) total')
            ->groupBy('complaintable_type')
            ->pluck('total', 'complaintable_type');
    }

    /**
     * Top Users
     */
    public function topUsers()
    {
        return Complaint::selectRaw('user_id, COUNT(*) total')
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
        return Complaint::selectRaw('complaintable_type, complaintable_id, COUNT(*) total')
            ->groupBy('complaintable_type', 'complaintable_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}