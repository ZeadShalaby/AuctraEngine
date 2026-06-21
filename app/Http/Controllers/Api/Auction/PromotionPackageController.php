<?php

namespace App\Http\Controllers\Api\Auction;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\PromotionPackageRepositoryInterface;
use Illuminate\Http\Request;

class PromotionPackageController extends Controller
{
    //

    public function __construct(protected PromotionPackageRepositoryInterface $promotionPackageRepository)
    {}

    public function index()
    {
        return successResponse(__("messages.success"), $this->promotionPackageRepository->all());
    }

    public function show(int $id)
    {
        return successResponse(__("messages.success"), $this->promotionPackageRepository->find($id));
    }



}
