<?php
namespace App\Repositories\Interfaces;

interface SubCategoryRepositoryInterface {

    public function all($perPage = 10, $category_id = null);

    public function find(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

}