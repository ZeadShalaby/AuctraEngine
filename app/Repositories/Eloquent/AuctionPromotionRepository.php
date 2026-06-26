<?php

namespace App\Repositories\Eloquent;

use App\Enums\PaymentGateway;
use App\Enums\PaymentType;
use App\Enums\PromotionStatus;
use App\Models\AuctionPromotion;
use App\Models\PromotionPackage;
use App\Models\Wallet\Payment;
use App\Models\Wallet\Transaction;
use App\Repositories\Interfaces\AuctionPromotionRepositoryInterface;
use App\Services\PaymentManager;
use Illuminate\Support\Facades\DB;

class AuctionPromotionRepository implements AuctionPromotionRepositoryInterface
{
    public function __construct(protected AuctionPromotion $auctionPromotion, protected PaymentManager $paymentManager)
    {
    }

    private function ChangeStatus($type, $promotionPackage, $auctionId, $merchant_ref)
    {
        $status = match ($type) {
            PaymentGateway::MOAMALAT->value => PromotionStatus::PENDING->value,
            default => PromotionStatus::ACTIVE->value,
        };

        $auctionPromotion = AuctionPromotion::create([
            'auction_id' => $auctionId,
            'promotion_package_id' => $promotionPackage->id,
            'price' => $promotionPackage->price,
            'starts_at' => now(),
            'expires_at' => now()->addDays($promotionPackage->duration),
            'status' => $status,
        ]);

        $this->handlePaymentUpdate($type, $auctionPromotion, $merchant_ref, $promotionPackage);
    }

    private function handlePaymentUpdate($type, $promotion, $merchant_ref, $package)
    {
        match ($type) {
            PaymentGateway::MOAMALAT->value => $this->updatePaymentModel($merchant_ref, $promotion),
            default => $this->updateTransactionModel($package, $promotion),
        };
    }

    private function updatePaymentModel($merchant_ref, $promotion)
    {
        Payment::where('merchant_ref', $merchant_ref)->update([
            'payable_type' => get_class($promotion),
            'payable_id' => $promotion->id
        ]);
    }

    private function updateTransactionModel($package, $promotion)
    {
        Transaction::where('user_id', auth()->id())
            ->where('source_id', $package->id)
            ->where('source_type', get_class($package))
            ->update([
                'source_id' => $promotion->id,
                'source_type' => get_class($promotion)
            ]);
    }

    
    public function my($type, $perPage = 10)
    {
        $auctionIds = auth()->user()
            ->auctions()
            ->pluck('id');

        return $this->auctionPromotion
            ->whereIn('auction_id', $auctionIds)
            ->where('status', $type)
            ->paginate($perPage);
    }

    public function subcription(int $id, $auctionId, $type = null)
    {
        $promotionPackage = PromotionPackage::findOrFail($id);
        return DB::transaction(function () use ($type, $auctionId, $promotionPackage) {
            $payment = $this->paymentManager->handlePayment($type, auth()->user(), $promotionPackage, $promotionPackage->price, PaymentType::AUCTION_PROMOTION->value);
            $this->ChangeStatus($type, $promotionPackage, $auctionId, $payment->merchant_ref);
            return $payment;
        });
    }

    public function allPromotions($type, $perPage = 10)
    {
        return $this->auctionPromotion::with('package', 'auction')
            ->whereHas('package', function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->paginate($perPage);
    }
    // !admin
    public function all($type = null, $perPage = 10)
    {
        return $this->auctionPromotion->all()->paginate($perPage);
    }

    public function find($id)
    {
        return $this->auctionPromotion->findOrFail($id);
    }

}