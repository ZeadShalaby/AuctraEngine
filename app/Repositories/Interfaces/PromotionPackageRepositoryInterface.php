<?php
namespace App\Repositories\Interfaces;


interface PromotionPackageRepositoryInterface
{
    public function all($perPage = 10);
    public function find(int $id);   
    //! admin
    public function store(array $data);
    public function update(array $data, int $id);
    public function destroy(int $id);
}