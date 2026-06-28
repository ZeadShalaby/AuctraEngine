<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Auction;
use App\Models\Setting;
use App\Models\Wallet\Transaction;
use Illuminate\Support\Facades\DB;

class AuctionTermsRefundService
{
    /**
     * Refund auction terms fees after winner completes payment.
     */
    public function refund(Auction $auction): void
    {
        DB::transaction(function () use ($auction) {
            $auction->load([
                'termsBought.user'
            ]);

            foreach ($auction->termsBought as $term) {

                if ($term->is_refunded) {
                    continue;
                }

                if ($term->user_id == $auction->winner_id) {
                    continue;
                }

                $refundAmount = $this->calculateRefundAmount($term->amount);

                if ($refundAmount <= 0) {
                    continue;
                }

                incrementWallet(
                    $term->user,
                    $refundAmount
                );

                Transaction::create([
                    'user_id' => $term->user_id,
                    'amount' => $refundAmount,
                    'type' => PaymentType::AuctionTermsRefund->value,
                    'status' => PaymentStatus::COMPLETED->value,
                    'source_type' => get_class($term),
                    'source_id' => $term->id,
                    'description' => "Payment for " . get_class($term),
                ]);

                $term->update([
                    'is_refunded' => true,
                ]);
            }
        });
        DB::commit();
    }

    /**
     * ?Calculate refund amount.
     */
    private function calculateRefundAmount(float $paidAmount): float
    {
        $discount = (float) Setting::query()
            ->where('key', 'auction_terms_discount')
            ->value('value') ?? 0;

        return max($paidAmount - $discount, 0);
    }
}