<?php
namespace App\Enums;

enum AdsStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case LIVE = 'live'; // ! when he old user and can trust him
    case ACTIVE = 'active'; // ! when the ad is active and running

}