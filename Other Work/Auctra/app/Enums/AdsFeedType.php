<?php
namespace App\Enums;

enum AdsFeedType: string
{
    case POSTS = 'posts';
    case REELS = 'reels';
    case BOTH = 'both';

}