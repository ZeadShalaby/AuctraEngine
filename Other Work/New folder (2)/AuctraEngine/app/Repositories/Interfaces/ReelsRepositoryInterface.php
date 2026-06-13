<?php

namespace App\Repositories\Interfaces;

interface ReelsRepositoryInterface
{
    public function allReels($perPage = 15);

    public function showReel(int $id);

    public function createReel(array $data);

    public function updateReel(int $id, array $data);

    public function deleteReel(int $id);

    public function searchReels(string $query);

    public function userReels(int $userId, $perPage = 15);

    public function myReels($perPage = 15);

    public function recommendedReels($perPage = 15);
}