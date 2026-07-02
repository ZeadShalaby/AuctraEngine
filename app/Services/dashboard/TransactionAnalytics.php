<?php

namespace App\Services\Dashboard;

use App\Models\Wallet\Transaction;

class TransactionAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Transaction::count(),

            'today' => Transaction::whereDate('created_at', today())->count(),

            'this_month' => Transaction::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'total_amount' => Transaction::where('status', 'completed')
                ->sum('amount'),
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

            'this_week' => Transaction::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Amounts
            |--------------------------------------------------------------------------
            */

            'completed_amount' => Transaction::where('status', 'completed')
                ->sum('amount'),

            'pending_amount' => Transaction::where('status', 'pending')
                ->sum('amount'),

            'failed_amount' => Transaction::where('status', 'failed')
                ->sum('amount'),

            'average_transaction_amount' => round(
                Transaction::where('status', 'completed')->avg('amount'),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'completed_transactions' => Transaction::where('status', 'completed')->count(),

            'pending_transactions' => Transaction::where('status', 'pending')->count(),

            'failed_transactions' => Transaction::where('status', 'failed')->count(),

            /*
            |--------------------------------------------------------------------------
            | Types
            |--------------------------------------------------------------------------
            */

            'deposit_transactions' => Transaction::where('type', 'deposit')->count(),

            'withdraw_transactions' => Transaction::where('type', 'withdraw')->count(),

            'bid_hold_transactions' => Transaction::where('type', 'bid_hold')->count(),

            'bid_release_transactions' => Transaction::where('type', 'bid_release')->count(),

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            'active_users' => Transaction::distinct('user_id')
                ->count('user_id'),

            'average_transactions_per_user' => round(
                Transaction::count() /
                max(Transaction::distinct('user_id')->count('user_id'), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'largest_transaction' => Transaction::orderByDesc('amount')
                ->first(),

            'most_used_type' => Transaction::selectRaw('type, COUNT(*) total')
                ->groupBy('type')
                ->orderByDesc('total')
                ->first(),

            'most_active_user' => Transaction::selectRaw('user_id, COUNT(*) total')
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_transactions' => Transaction::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Transactions
     */
    public function monthlyChart()
    {
        return Transaction::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Monthly Amounts
     */
    public function monthlyAmountChart()
    {
        return Transaction::selectRaw('MONTH(created_at) month, SUM(amount) total')
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Transaction Types
     */
    public function typeChart()
    {
        return Transaction::selectRaw('type, COUNT(*) total')
            ->groupBy('type')
            ->pluck('total', 'type');
    }

    /**
     * Transaction Status
     */
    public function statusChart()
    {
        return Transaction::selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Top Users
     */
    public function topUsers()
    {
        return Transaction::selectRaw('user_id, COUNT(*) total')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }

    /**
     * Top Transactions
     */
    public function topTransactions()
    {
        return Transaction::orderByDesc('amount')
            ->take(10)
            ->get();
    }
}