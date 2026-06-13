<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Exception;
use Override;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(protected Category $category)
    {
    }

    public function all($perPage = 10)
    {
        return $this->category::paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->category::findOrFail($id);
    }

    //! admin
    public function create(array $data)
    {
        addMediaIfExists(Category::class, $data, 'image');
        return $this->category::create($data);
    }

    //! admin
    public function update(int $id, array $data)
    {
        addMediaIfExists(Category::class, $data, 'image');
        $category = $this->category::findOrFail($id);
        $category->update($data);
        return $category;
    }

    //! admin
    public function delete(int $id)
    {
        $category = $this->category::findOrFail($id);
        $category->delete();
        return true;
    }

}