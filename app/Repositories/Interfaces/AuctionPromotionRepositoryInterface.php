<?php

namespace App\Repositories\Interfaces;

interface AuctionPromotionRepositoryInterface
{
    public function my($type, $perPage = 10);
    public function subcription(int $id,$auctionId, $type = null);
    // !admin
    public function all($perPage = 10);
    public function find(int $id);


}