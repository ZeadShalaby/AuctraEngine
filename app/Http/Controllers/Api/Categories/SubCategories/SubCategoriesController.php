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
        return successResponse(__("messages.success"), $this->subCategoryRepository->all($request->query('paginate', 10), $request->query('category_id') ?? null));
    }

    public function show(int $id)
    {
        return successResponse(__("messages.success"),$this->subCategoryRepository->find($id));
    }
}
