<?php

namespace App\Http\Controllers\Api\Interests;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\Interfaces\InterestsRepositoryInterface;
use Illuminate\Http\Request;

class InterestsController extends Controller
{
    //

    public function __construct(protected InterestsRepositoryInterface $interestsRepository)
    {
    }

    public function index(Request $request)
    {
        return $this->interestsRepository->my(perPage: $request->input('paginate', 10));
    }

    public function toggle(Category $category)
    {
        $result = $this->interestsRepository->toggle($category->id);
        return successResponse(__("Interest toggled successfully"), $result, 200);
    }
}
