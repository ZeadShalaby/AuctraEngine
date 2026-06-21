<?php

namespace App\Services;

use App\Contracts\PaymentStrategy;
use App\Services\Payments\CardPayment;
use App\Services\Payments\MoamalatPayment;
use App\Services\Payments\WalletPayment;
use Exception;

class PaymentManager
{
    public function handlePayment(string $gateway, $user, $payable, $price, $type = null)
    {
        $strategy = $this->resolveStrategy($gateway);
        return $strategy->pay($user, $payable, $price, $type);
    }

    protected function resolveStrategy(string $gateway): PaymentStrategy
    {
        return match ($gateway) {
            'wallet' => app(WalletPayment::class),
            'moamalat' => app(MoamalatPayment::class),
            'card' => app(CardPayment::class),
            default => throw new Exception("Gateway [{$gateway}] not supported."),
        };
    }
}