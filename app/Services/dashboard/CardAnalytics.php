<?php

namespace App\Services\Dashboard;

use App\Models\Card;

class CardAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Card::count(),

            'active' => Card::where('status', 'active')->count(),

            'inactive' => Card::where('status', 'inactive')->count(),

            'today' => Card::whereDate('created_at', today())->count(),
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

            'this_week' => Card::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => Card::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Types
            |--------------------------------------------------------------------------
            */

            'normal_cards' => Card::where('type', 'normal')->count(),

            'subscription_cards' => Card::where('type', 'subscription')->count(),

            'other_cards' => Card::where('type', 'other')->count(),

            /*
            |--------------------------------------------------------------------------
            | Prices
            |--------------------------------------------------------------------------
            */

            'selling_price_total' => Card::sum('selling_price'),

            'average_selling_price' => round(Card::avg('selling_price'), 2),

            'highest_selling_price' => Card::max('selling_price'),

            'lowest_selling_price' => Card::min('selling_price'),

            'average_recharge_amount' => round(Card::avg('recharge_amount'), 2),

            'highest_recharge_amount' => Card::max('recharge_amount'),

            /*
            |--------------------------------------------------------------------------
            | Cost
            |--------------------------------------------------------------------------
            */

            'total_cost' => Card::sum('amount'),

            'average_cost' => round(Card::avg('amount'), 2),

            /*
            |--------------------------------------------------------------------------
            | Profit
            |--------------------------------------------------------------------------
            */

            'expected_profit' => Card::sum('selling_price') - Card::sum('amount'),

            /*
            |--------------------------------------------------------------------------
            | Companies
            |--------------------------------------------------------------------------
            */

            'companies' => Card::distinct('company_id')
                ->count('company_id'),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_cards' => Card::latest()
                ->take(10)
                ->get(),

            'highest_price_cards' => Card::orderByDesc('selling_price')
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Cards Per Month
     */
    public function monthlyChart()
    {
        return Card::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Status Chart
     */
    public function statusChart()
    {
        return Card::selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Type Chart
     */
    public function typeChart()
    {
        return Card::selectRaw('type, COUNT(*) total')
            ->groupBy('type')
            ->pluck('total', 'type');
    }

    /**
     * Companies Chart
     */
    public function companyChart()
    {
        return Card::selectRaw('company_id, COUNT(*) total')
            ->groupBy('company_id')
            ->pluck('total', 'company_id');
    }

    /**
     * Top Selling Price Cards
     */
    public function topCards()
    {
        return Card::orderByDesc('selling_price')
            ->take(10)
            ->get();
    }
}