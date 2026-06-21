<?php

namespace App\Enums;

enum PaymentType: string {
    case AD_FEE = 'ad_fee';
    case AUCTION_TERMS = 'auction_terms_fee';
    case AUCTION_PROMOTION = 'auction_promotion';
    case AUCTION_WIN = 'auction_win';
    case WALLET_DEPOSIT = 'deposit';

}