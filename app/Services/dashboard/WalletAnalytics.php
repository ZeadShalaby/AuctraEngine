<?php

namespace App\Services\Dashboard;

use App\Models\Wallet\Wallet;
use App\Models\Wallet\WalletLog;

class WalletAnalytics
{
    /**

     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total_wallets' => Wallet::count(),

            'total_balance' => Wallet::sum('balance'),

            'total_reserved_balance' => Wallet::sum('reserved_balance'),

            'today_logs' => WalletLog::whereDate('created_at', today())->count(),

            'this_month_logs' => WalletLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
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

            'this_week_logs' => WalletLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Wallets
            |--------------------------------------------------------------------------
            */

            'active_wallets' => Wallet::where('balance', '>', 0)->count(),

            'empty_wallets' => Wallet::where('balance', '<=', 0)->count(),

            'average_balance' => round(
                Wallet::avg('balance'),
                2
            ),

            'average_reserved_balance' => round(
                Wallet::avg('reserved_balance'),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Transactions
            |--------------------------------------------------------------------------
            */

            'total_transactions' => WalletLog::count(),

            'total_deposits' => WalletLog::where('type', 'deposit')->sum('amount'),

            'total_withdraws' => WalletLog::where('type', 'withdraw')->sum('amount'),

            'total_bid_holds' => WalletLog::where('type', 'bid_hold')->sum('amount'),

            'total_bid_releases' => WalletLog::where('type', 'bid_release')->sum('amount'),

            /*
            |--------------------------------------------------------------------------
            | Counters
            |--------------------------------------------------------------------------
            */

            'deposit_count' => WalletLog::where('type', 'deposit')->count(),

            'withdraw_count' => WalletLog::where('type', 'withdraw')->count(),

            'bid_hold_count' => WalletLog::where('type', 'bid_hold')->count(),

            'bid_release_count' => WalletLog::where('type', 'bid_release')->count(),

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            'wallet_owners' => Wallet::distinct('user_id')
                ->count('user_id'),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'largest_wallet' => Wallet::orderByDesc('balance')
                ->first(),

            'largest_transaction' => WalletLog::orderByDesc('amount')
                ->first(),

            'most_used_transaction_type' => WalletLog::selectRaw('type, COUNT(*) total')
                ->groupBy('type')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_transactions' => WalletLog::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Transactions
     */
    public function monthlyChart()
    {
        return WalletLog::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Monthly Amounts
     */
    public function monthlyAmountChart()
    {
        return WalletLog::selectRaw('MONTH(created_at) month, SUM(amount) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Transaction Types Chart
     */
    public function transactionTypesChart()
    {
        return WalletLog::selectRaw('type, COUNT(*) total')
            ->groupBy('type')
            ->pluck('total', 'type');
    }

    /**
     * Top Wallets
     */
    public function topWallets()
    {
        return Wallet::orderByDesc('balance')
            ->take(10)
            ->get();
    }

    /**
     * Top Transactions
     */
    public function topTransactions()
    {
        return WalletLog::orderByDesc('amount')
            ->take(10)
            ->get();
    }
}