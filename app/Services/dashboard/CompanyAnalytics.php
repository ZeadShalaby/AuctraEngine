<?php

namespace App\Services\Dashboard;

use App\Models\Card;
use App\Models\Company;

class CompanyAnalytics
{
    /**
     * Dashboard Analytics
     */
    public function dashboard(): array
    {
        return [
            'total' => Company::count(),

            'active' => Company::where('status', 'active')->count(),

            'inactive' => Company::where('status', 'inactive')->count(),

            'today' => Company::whereDate('created_at', today())->count(),
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

            'this_week' => Company::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),

            'this_month' => Company::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            /*
            |--------------------------------------------------------------------------
            | Cards
            |--------------------------------------------------------------------------
            */

            'cards_count' => Card::count(),

            'companies_with_cards' => Card::distinct('company_id')
                ->count('company_id'),

            'average_cards_per_company' => round(
                Card::count() /
                max(Company::count(), 1),
                2
            ),

            /*
            |--------------------------------------------------------------------------
            | Top Companies
            |--------------------------------------------------------------------------
            */

            'most_cards_company' => Card::selectRaw('company_id, COUNT(*) total')
                ->groupBy('company_id')
                ->orderByDesc('total')
                ->first(),

            /*
            |--------------------------------------------------------------------------
            | Latest
            |--------------------------------------------------------------------------
            */

            'latest_companies' => Company::latest()
                ->take(10)
                ->get(),
        ]);
    }

    /**
     * Companies Per Month
     */
    public function monthlyChart()
    {
        return Company::selectRaw('MONTH(created_at) month, COUNT(*) total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');
    }

    /**
     * Status Chart
     */
    public function statusChart()
    {
        return Company::selectRaw('status, COUNT(*) total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Cards Per Company
     */
    public function cardsChart()
    {
        return Card::selectRaw('company_id, COUNT(*) total')
            ->groupBy('company_id')
            ->pluck('total', 'company_id');
    }

    /**
     * Top Companies
     */
    public function topCompanies()
    {
        return Card::selectRaw('company_id, COUNT(*) total')
            ->groupBy('company_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();
    }
}