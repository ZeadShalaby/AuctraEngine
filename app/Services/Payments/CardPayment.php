<?php

namespace App\Services\Payments;
use App\Contracts\PaymentStrategy;
use App\Enums\PaymentStatus;

class CardPayment implements PaymentStrategy {
    public function pay($user, $payable, $price ,$type = null) {
        incrementWallet($user, $price);
        return transaction(
            user_id: $user->id,
            amount: $price,
            type: $type,
            status: PaymentStatus::COMPLETED->value,
            source_type: get_class($payable),
            source_id: $payable->id,
            description: "Payment for " . get_class($payable)
        );
    }
}