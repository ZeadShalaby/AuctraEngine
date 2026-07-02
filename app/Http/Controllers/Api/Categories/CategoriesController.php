<?php

namespace App\Http\Controllers\Api\Categories;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //
    public function __construct(protected CategoryRepositoryInterface $categoryRepository) {}


    public function index(Request $request)
    {
        return successResponse(__("messages.success"), $this->categoryRepository->all($request->integer('paginate', 10)));
    }

    public function show(int $id)
    {
        return successResponse(__("messages.success"), $this->categoryRepository->find($id));
    }
}
