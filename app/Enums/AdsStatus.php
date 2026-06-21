<?php
namespace App\Enums;

enum AdsStatus: string
{
    case PENDING = 'pending';
    case ENDED = 'ended';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case REVIEW = 'review'; // تم الدفع، بس محتاج موافقة الإدارة
    case LIVE = 'live'; // ! when he old user and can trust him
    case ACTIVE = 'active'; // ! when the ad is active and running


}