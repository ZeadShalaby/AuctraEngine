<?php

namespace App\Enums;

enum ComplaintStatus: string
{

    case PENDING = 'pending';
    case REVIEWED = 'reviewed';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';

}