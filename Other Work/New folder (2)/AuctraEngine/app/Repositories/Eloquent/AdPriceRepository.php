<?php

namespace App\Repositories;

use App\Models\AdPrice;
use App\Repositories\Interfaces\AdPriceRepositoryInterface;

class AdPriceRepository implements AdPriceRepositoryInterface
{

    public function __construct(protected AdPrice $adprice){}
    public function all($perPage = 10)
    {
        return $this->adprice::paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->adprice::findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->adprice::create($data);
    }

    public function update(int $id, array $data)
    {
        $price = $this->adprice::findOrFail($id);

        $price->update($data);

        return $price;
    }

    public function delete(int $id)
    {
        $price = $this->adprice::findOrFail($id);

        return $price->delete();
    }

    public function getPriceByPlacement(string $placement)
    {
        return $this->adprice::where('placement', $placement)
            ->where('is_active', 1)
            ->first();
    }
}