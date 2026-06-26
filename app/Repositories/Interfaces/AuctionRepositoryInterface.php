<?php

namespace App\Repositories\Interfaces;

interface AuctionRepositoryInterface
{
    public function all(array $filters = [], int $perPage = 15);

    public function my(array $filters = [], int $perPage = 15);

    public function find(int $id);
 
    public function myAuctionWinner();
 
    public function create(array $data);

    public function update(int $id, array $data);

    public function incrementBidsCount(int $auctionId);

    public function setWinner(int $auctionId, int $userId, string $price);

    public function endAuction(int $auctionId);

    public function buyTerms($auctionId, $userId);

    public function deleteAuction(int $id);
}