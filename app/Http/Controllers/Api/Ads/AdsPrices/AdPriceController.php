<?php

namespace App\Http\Controllers\Api\Ads\AdsPrices;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\AdPriceRepositoryInterface;

class AdPriceController extends Controller
{
    public function __construct(protected AdPriceRepositoryInterface $adPriceRepository){}

    public function index($perPage = 10)
    {
        $prices = $this->adPriceRepository->all($perPage);
        return successResponse(__("messages.success"), $prices);
    }

    public function show(int $id)
    {
        $price = $this->adPriceRepository->find($id);
        return successResponse(__("messages.success"), $price);
    }

}
