<?php

namespace App\Services\Dashboard;

use App\Models\RechargeCard;

class RechargeCardAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => RechargeCard::count(),

            'today' => RechargeCard::whereDate('created_at', today())->count(),

            'this_month' => RechargeCard::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            'used_cards' => RechargeCard::where('used', true)->count(),

            'unused_cards' => RechargeCard::where('used', false)->count(),

            'total_balance' => RechargeCard::sum('recharge_amount'),
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

            'this_week' => RechargeCard::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            /*
            |--------------------------------------------------------------------------
            | Usage
            |--------------------------------------------------------------------------
            */

            'usage_rate' => round(
                (RechargeCard::where('used', true)->count() * 100) /
                max(RechargeCard::count(), 1),
                2
            ),

            'unused_rate' => round(
                (RechargeCard::where('used', false)->count() * 100) /
                max(RechargeCard::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Amounts
            |--------------------------------------------------------------------------
            */

            'used_balance' => RechargeCard::where('used', true)
                ->sum('recharge_amount'),

            'unused_balance' => RechargeCard::where('used', false)
                ->sum('recharge_amount'),

            'average_recharge_amount' => round(
                RechargeCard::avg('recharge_amount'),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Cards
            |--------------------------------------------------------------------------
            */

            'cards_types' => RechargeCard::distinct('card_id')
                ->count('card_id'),

            /*
            |--------------------------------------------------------------------------
            | Top
            |--------------------------------------------------------------------------
            */

            'highest_recharge_card' => RechargeCard::orderByDesc('recharge_amount')
                ->first(),

            'most_generated_card_type' => RechargeCard::selectRaw('card_id, COUNT(*) total')
                ->groupBy('card_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_cards' => RechargeCard::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Monthly Recharge Cards
     */
    public function monthlyChart()
    {
        return RechargeCard::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Monthly Recharge Amount
     */
    public function monthlyAmountChart()
    {
        return RechargeCard::selectRaw('MONTH(created_at) month, SUM(recharge_amount) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Cards Per Type
     */
    public function cardTypesChart()
    {
        return RechargeCard::selectRaw('card_id, COUNT(*) total')
            ->groupBy('card_id')
            ->pluck('total', 'card_id');
    }

    /**
     * Top Recharge Cards
     */
    public function topRechargeCards()
    {
        return RechargeCard::orderByDesc('recharge_amount')
            ->take(10)
            ->get();
    }

    /**
     * Top Card Types
     */
    public function topCardTypes()
    {
        return RechargeCard::selectRaw('card_id, COUNT(*) total')
            ->groupBy('card_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}