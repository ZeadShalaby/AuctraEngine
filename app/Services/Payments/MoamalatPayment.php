<?php

namespace App\Services\Payments;
use App\Contracts\PaymentStrategy;
use App\Enums\PaymentStatus;

class MoamalatPayment implements PaymentStrategy
{
    public function pay($user, $payable, $price, $type = null)
    {
        $merchantRef = $type . '-' . time() . '-' . $payable->id;
        $payment = payment(
            user_id: $user->id,
            merchant_ref: $merchantRef,
            amount: $price,
            status: PaymentStatus::PENDING->value,
            payment_gateway: $data['gateway_name'] ?? 'moamalat',
            type: $type,
            payable_type: get_class($payable),
            payable_id: $payable->id,
            details: null
        );
        return $payment;
    }
}