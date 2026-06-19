<?php
namespace App\Enums;

enum AuctionStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case CANCELLED = 'cancelled'; 
    case ACTIVE = 'active'; 
    case ENDED = 'ended';

}