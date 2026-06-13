<?php

namespace App\Http\Controllers\Api\Ads;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use Illuminate\Http\Request;
class AdsController extends Controller
{

    public function __construct(protected AdsRepositoryInterface $adsRepo){}

    public function index(Request $request)
    {
        $ads = $this->adsRepo->all($request->key, $request->start, $request->end);
        return successResponse(__("messages.success"), $ads, 200);
    }

    public function show(int $id)
    {
        $ad = $this->adsRepo->find($id);
        return successResponse(__("messages.success"), $ad, 200);
    }

    public function create(Request $request)
    {
        $ads = $this->adsRepo->create($request->all());
        return successResponse(__("messages.created"), $ads, 201);
    }

}
