<?php

namespace App\Filters;

use App\Enums\AuctionStatus;
use Illuminate\Database\Eloquent\Builder;

class AuctionFilter
{
    public function __construct(
        protected array $filters = []
    ) {
    }

    public function apply(Builder $query): Builder
    {
        return $query

            ->when(
                $this->filters['category_id'] ?? null,
                fn($q, $value) => $q->where('category_id', $value)
            )

            ->when(
                $this->filters['subcategory_id'] ?? null,
                fn($q, $value) => $q->where('sub_category_id', $value)
            )

            ->when(
                $this->filters['condition'] ?? null,
                fn($q, $value) => $q->where('condition', $value)
            )

            ->when(
                $this->filters['price_from'] ?? null,
                function ($q, $value) {
                    $q->where(function ($query) use ($value) {
                        $query->where('start_price', '>=', $value)
                            ->orWhere('current_price', '>=', $value);
                    });
                }
            )

            ->when(
                $this->filters['price_to'] ?? null,
                function ($q, $value) {
                    $q->where(function ($query) use ($value) {
                        $query->where('start_price', '<=', $value)
                            ->orWhere('current_price', '<=', $value);
                    });
                }
            )

            ->when(
                $this->filters['type'] ?? null,
                function ($query, $type) {

                    switch ($type) {

                        case 'live':

                            $query
                                ->where('status', AuctionStatus::ACTIVE->value)
                                ->where('start_at', '<=', now())
                                ->where('end_at', '>', now());

                            break;

                        case 'cancelled':

                            $query
                                ->where('status', AuctionStatus::CANCELLED->value);
                            break;

                        case 'completed':

                            $query
                                ->where('status', AuctionStatus::ENDED->value);
                            break;

                        case 'buy_now':

                            $query
                                ->where('buy_now_price', '>', 0);

                            break;

                        case 'ending_soon':

                            $query
                                ->where('status', 'active')
                                ->where('end_at', '>', now())
                                ->where('end_at', '<=', now()->addHours(24));

                            break;
                    }
                }
            )

            ->when(
                $this->filters['sort'] ?? null,
                function ($q, $sort) {

                    match ($sort) {
                        'price_asc' => $q->orderBy('price'),
                        'price_desc' => $q->orderByDesc('price'),
                        'oldest' => $q->oldest(),
                        default => $q->latest(),
                    };
                },
                fn($q) => $q->latest()
            );
    }
}