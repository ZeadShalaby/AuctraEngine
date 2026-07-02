<?php

namespace App\Services\Dashboard;

use App\Models\reports\ReportAction;

class ReportActionAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => ReportAction::count(),

            'today' => ReportAction::whereDate('created_at', today())->count(),

            'this_month' => ReportAction::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'admins' => ReportAction::distinct('admin_id')
                ->count('admin_id'),
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

            'this_week' => ReportAction::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Reports
            |--------------------------------------------------------------------------
            */

            'reports_handled' => ReportAction::distinct('report_id')
                ->count('report_id'),

            'average_actions_per_report' => round(
                ReportAction::count() /
                max(ReportAction::distinct('report_id')->count('report_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Admins
            |--------------------------------------------------------------------------
            */

            'active_admins' => ReportAction::distinct('admin_id')
                ->count('admin_id'),

            'average_actions_per_admin' => round(
                ReportAction::count() /
                max(ReportAction::distinct('admin_id')->count('admin_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Actions
            |--------------------------------------------------------------------------
            */

            'delete_post_actions' => ReportAction::where('action', 'delete_post')->count(),

            'delete_reel_actions' => ReportAction::where('action', 'delete_reel')->count(),

            'ban_user_actions' => ReportAction::where('action', 'ban_user')->count(),

            'none_actions' => ReportAction::where('action', 'none')->count(),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'most_used_action' => ReportAction::selectRaw('action, COUNT(*) total')
                ->groupBy('action')
                ->orderByDesc('total')
                ->first(),

            'most_active_admin' => ReportAction::selectRaw('admin_id, COUNT(*) total')
                ->groupBy('admin_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_actions' => ReportAction::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Actions
     */
    public function monthlyChart()
    {
        return ReportAction::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Actions Chart
     */
    public function actionsChart()
    {
        return ReportAction::selectRaw('action, COUNT(*) total')
            ->groupBy('action')
            ->pluck('total', 'action');
    }

    /**
     * Top Admins
     */
    public function topAdmins()
    {
        return ReportAction::selectRaw('admin_id, COUNT(*) total')
            ->groupBy('admin_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Actions
     */
    public function topActions()
    {
        return ReportAction::selectRaw('action, COUNT(*) total')
            ->groupBy('action')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}