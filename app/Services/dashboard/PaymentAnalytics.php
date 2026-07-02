<?php

namespace App\Services\Dashboard;

use App\Models\Wallet\Payment;

class PaymentAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Payment::count(),
            'total_amount' => Payment::where('status', 'success')->sum('amount'),
            'success' => Payment::where('status', 'success')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'revenue' => Payment::where('status', 'success')->sum('amount'),
            'failed' => Payment::where('status', 'failed')->count(),
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

            'failed' => Payment::where('status', 'failed')->count(),
            'failed_amount' => Payment::where('status', 'failed')->sum('amount'),

            'success_rate' => round(
                Payment::where('status', 'success')->count() * 100 /
                max(Payment::count(), 1),
                2
            ),

            'pending' => Payment::where('status', 'pending')->count(),
            'pending_amount' => Payment::where('status', 'pending')->sum('amount'),
            'pending_rate' => round(
                Payment::where('status', 'pending')->count() * 100 /
                max(Payment::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'today' => Payment::whereDate('created_at', today())->count(),

            'today_revenue' => Payment::where('status', 'success')
                ->whereDate('created_at', today())
                ->sum('amount'),

            'this_week' => Payment::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => Payment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'month_revenue' => Payment::where('status', 'success')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),

            /*
            |--------------------------------------------------------------------------
            | Amount
            |--------------------------------------------------------------------------
            */

            'average_payment' => round(
                Payment::where('status', 'success')->avg('amount'),
                2
            ),

            'highest_payment' => Payment::where('status', 'success')->max('amount'),

            'lowest_payment' => Payment::where('status', 'success')->min('amount'),

            /*
            |--------------------------------------------------------------------------
            | Types
            |--------------------------------------------------------------------------
            */

            'deposit' => Payment::where('type', 'deposit')->count(),

            'withdraw' => Payment::where('type', 'withdraw')->count(),

            'auction_terms' => Payment::where('type', 'auction_terms')->count(),

            'auction_promotion' => Payment::where('type', 'auction_promotion')->count(),

            'ads' => Payment::where('type', 'ads')->count(),

            /*
            |--------------------------------------------------------------------------
            | Gateways
            |--------------------------------------------------------------------------
            */

            'paypal' => Payment::where('payment_gateway', 'paypal')->count(),

            'stripe' => Payment::where('payment_gateway', 'stripe')->count(),

            'moamalat' => Payment::where('payment_gateway', 'moamalat')->count(),

            'other_gateway' => Payment::where('payment_gateway', 'other')->count(),

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            'unique_users' => Payment::distinct('user_id')
                ->count('user_id'),

            'top_user' => Payment::selectRaw('user_id, SUM(amount) total')
                ->where('status', 'success')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_payments' => Payment::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Revenue
     */
    public function revenueChart()
    {
        return Payment::selectRaw('MONTH(created_at) month, SUM(amount) total')
            ->where('status', 'success')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Monthly Payments
     */
    public function monthlyChart()
    {
        return Payment::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Status Chart
     */
    public function statusChart()
    {
        return Payment::selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Payment Type Chart
     */
    public function typeChart()
    {
        return Payment::selectRaw('type, COUNT(*) total')
            ->groupBy('type')
            ->pluck('total', 'type');
    }

    /**
     * Gateway Chart
     */
    public function gatewayChart()
    {
        return Payment::selectRaw('payment_gateway, COUNT(*) total')
            ->groupBy('payment_gateway')
            ->pluck('total', 'payment_gateway');
    }

    /**
     * Top Users
     */
    public function topUsers()
    {
        return Payment::selectRaw('user_id, SUM(amount) total')
            ->where('status', 'success')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}