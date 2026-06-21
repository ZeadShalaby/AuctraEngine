<?php

namespace App\Enums;

enum PromotionStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case REVIEW = 'review';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';

}