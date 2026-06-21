<?php

namespace App\Enums;

use App\Services\Payments\CardPayment;
use App\Services\Payments\MoamalatPayment;
use App\Services\Payments\WalletPayment;

enum PaymentGateway: string
{
    case WALLET = 'wallet';
    case MOAMALAT = 'moamalat';
    case CARD = 'card';

    public function getStrategy()
    {
        return match ($this) {
            self::WALLET => new WalletPayment(),
            self::MOAMALAT => new MoamalatPayment(),
            self::CARD => new CardPayment(),
        };
    }
}