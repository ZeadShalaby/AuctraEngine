<?php

namespace App\Repositories\Interfaces;

interface AuctionRepositoryInterface
{
    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function incrementBidsCount(int $auctionId);

    public function setWinner(int $auctionId, int $userId, string  $price);

    public function endAuction(int $auctionId);
}