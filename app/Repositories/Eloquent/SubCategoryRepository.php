<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\SubCategory;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;


class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    public function __construct(protected SubCategory $subCategory){}


    public function all($perPage = 10)
    {
        return $this->subCategory::with('category:id,name_ar,name_en')->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->subCategory::with('category:id,name_ar,name_en')->findOrFail($id);
    }

    //! admin
    public function create(array $data)
    {
        addMediaIfExists(Category::class, $data, 'image');
        return $this->subCategory::create($data);
    }

    //! admin
    public function update(int $id, array $data)
    {
        addMediaIfExists(SubCategory::class, $data, 'image');
        $category = $this->subCategory::findOrFail($id);
        $category->update($data);
        return $category;
    }

    //! admin
    public function delete(int $id)
    {
        $category = $this->subCategory::findOrFail($id);
        $category->delete();
        return true;
    }

}