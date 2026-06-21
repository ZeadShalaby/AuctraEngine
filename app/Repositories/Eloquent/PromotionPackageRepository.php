<?php
namespace App\Repositories\Eloquent;

use App\Models\PromotionPackage;
use App\Models\Wallet\Payment;
use App\Repositories\Interfaces\PromotionPackageRepositoryInterface;

class PromotionPackageRepository implements PromotionPackageRepositoryInterface
{
    public function __construct(protected PromotionPackage $promotionPackage){}

    public function all($perPage = 10)
    {
        return $this->promotionPackage->Active()->latest()->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->promotionPackage->findOrFail($id);
    }

    //! admin
    public function store(array $data)
    {
        return $this->promotionPackage->create($data);
    }

    public function update(array $data, int $id)
    {
        return $this->promotionPackage->findOrFail($id)->update($data);
    }

    public function destroy(int $id)
    {
        return $this->promotionPackage->findOrFail($id)->delete();
    }


}