<?php

namespace App\Repositories\Interfaces;

interface AuctionPromotionRepositoryInterface
{
    public function allPromotions($type, $perPage = 10);
    public function my($type, $perPage = 10);
    public function subcription(int $id,$auctionId, $type = null);
    // !admin
    public function all($type = null, $perPage = 10);
    public function find(int $id);

}