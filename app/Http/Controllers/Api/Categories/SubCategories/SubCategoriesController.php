<?php

namespace App\Http\Controllers\Api\Categories\SubCategories;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;
use Illuminate\Http\Request;

class SubCategoriesController extends Controller
{
    //
    public function __construct(protected SubCategoryRepositoryInterface $subCategoryRepository) {}


    public function index(Request $request)
    {
        return $this->subCategoryRepository->all($request->integer('paginate', 10));
    }

    public function show(int $id)
    {
        return $this->subCategoryRepository->find($id);
    }
}
