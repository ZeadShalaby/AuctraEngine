<?php

namespace App\Services\Dashboard;


use Illuminate\Support\Facades\Cache;

class DashboardAnalyticsService
{
    public function __construct(
        protected AdsAnalytics $ads,
        protected AuctionAnalytics $auctions,
        protected AuctionTermAnalytics $auctionTerms,
        protected AuctionWatcherAnalytics $auctionWatchers,
        protected AuctionPromotionAnalytics $auctionPromotions,
        protected BidAnalytics $bids,
        protected CardAnalytics $cards,
        protected CategoryAnalytics $categories,
        protected CommentAnalytics $comments,
        protected CompanyAnalytics $companies,
        protected ComplaintAnalytics $complaints,
        protected FavouriteAnalytics $favorites,
        protected InterestAnalytics $interests,
        protected LikeAnalytics $likes,
        protected PaymentAnalytics $payments,
        protected PostAnalytics $posts,
        protected PromotionPackageAnalytics $promotions,
        protected RechargeCardAnalytics $rechargeCards,
        protected ReelsAnalytics $reels,
        protected ReportActionAnalytics $reportActions,
        protected ReportAnalytics $reports,
        protected ReviewAnalytics $reviews,
        protected ShareAnalytics $shares,
        protected SubCategoryAnalytics $subCategories,
        protected TransactionAnalytics $transactions,
        protected UserAnalytics $users,
        protected UserInterestAnalytics $userInterests,
        protected WalletAnalytics $wallet,
    ) {
    }


    public function dashboard(): array
    {
        return Cache::remember('dashboard.analytics', 300, function () {

            return [

                'users' => $this->users->dashboard(),
                'ads' => $this->ads->dashboard(),
                'auctions' => $this->auctions->dashboard(),
                'payments' => $this->payments->dashboard(),
                'wallet' => $this->wallet->dashboard(),
                'reports' => $this->reports->dashboard(),
                'transactions' => $this->transactions->dashboard(),
            ];

        });
    }

    public function chartData(): array
    {
        return Cache::remember('dashboard.chartData', 300, function () {

            return [
                'users' => $this->users->citiesChart(),
                'usersByMonth' => $this->users->monthlyChart(),
                'usersByStatus' => $this->users->statusChart(),
                'usersByType' => $this->users->typeChart(),
                'usersByCountry' => $this->users->countriesChart(),
                'paymentsRevenue' => $this->payments->revenueChart(),
                'paymentsMonth' => $this->payments->monthlyChart(),
                'paymentsType' => $this->payments->typeChart(),
                'paymentsGateway' => $this->payments->gatewayChart(),
                'paymentsTopUsers' => $this->payments->topUsers(),
                'paymentsStatus' => $this->payments->statusChart(),
                'adsMonth' => $this->ads->monthlyChart(),
                'adsStatus' => $this->ads->statusChart(),
                'adsType' => $this->ads->feedTypeChart(),
                'auctionsMonth' => $this->auctions->monthlyChart(),
                'auctionsStatus' => $this->auctions->statusChart(),
                'auctionsCondition' => $this->auctions->conditionChart(),
                'auctionstopViewed' => $this->auctions->topViewed(),
                'auctionstopBids' => $this->auctions->topBids(),
                'auctionstopPrices' => $this->auctions->topPrices(),
                'monthlyChart' => $this->wallet->monthlyChart(),
                'monthlyAmountChart' => $this->wallet->monthlyAmountChart(),
                'transactionTypesChart' => $this->wallet->transactionTypesChart(),
                'topWallets' => $this->wallet->topWallets(),
                'topWalletTransactions' => $this->wallet->topTransactions(),
                'monthlyTransactions' => $this->transactions->monthlyChart(),
                'monthlyAmounts' => $this->transactions->monthlyAmountChart(),
                'transactionTypes' => $this->transactions->typeChart(),
                'transactionStatus' => $this->transactions->statusChart(),
                'topTransactionUsers' => $this->transactions->topUsers(),
                'topTransactions' => $this->transactions->topTransactions(),
                'monthlyReports' => $this->reports->monthlyChart(),
                'reportStatus' => $this->reports->statusChart(),
                'reportTypes' => $this->reports->typeChart(),
                'topReporters' => $this->reports->topUsers(),
                'topReportedItems' => $this->reports->topItems(),
            ];

        });
    }

    public function analytics(): array
    {
        return [

            'users' => $this->users->full(),
            'ads' => $this->ads->full(),
            'auctions' => $this->auctions->full(),
            'auction_terms' => $this->auctionTerms->full(),
            'auction_watchers' => $this->auctionWatchers->full(),
            'auction_promotions' => $this->auctionPromotions->full(),
            'bids' => $this->bids->full(),
            'cards' => $this->cards->full(),
            'categories' => $this->categories->full(),
            'comments' => $this->comments->full(),
            'companies' => $this->companies->full(),
            'complaints' => $this->complaints->full(),
            'favorites' => $this->favorites->full(),
            'interests' => $this->interests->full(),
            'likes' => $this->likes->full(),
            'posts' => $this->posts->full(),
            'promotion_packages' => $this->promotions->full(),
            'recharge_cards' => $this->rechargeCards->full(),
            'reels' => $this->reels->full(),
            'report_actions' => $this->reportActions->full(),
            'reports' => $this->reports->full(),
            'reviews' => $this->reviews->full(),
            'shares' => $this->shares->full(),
            'sub_categories' => $this->subCategories->full(),
            'transactions' => $this->transactions->full(),
            'user_interests' => $this->userInterests->full(),
            'wallet' => $this->wallet->full(),
            'payments' => $this->payments->full(),

        ];
    }
}
