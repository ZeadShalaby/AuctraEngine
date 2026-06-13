<?php

namespace App\Repositories\Interfaces;

interface AdPriceRepositoryInterface
{
    public function all($perPage = 10);

    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function getPriceByPlacement(string $placement);
}