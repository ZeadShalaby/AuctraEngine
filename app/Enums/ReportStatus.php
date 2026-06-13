<?php 
namespace App\Enums;

enum ReportStatus: string
{
    case PENDING = 'pending';
    case REVIEWED = 'reviewed';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';
}