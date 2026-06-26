<?php

namespace App\Http\Controllers\Api\Auction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auction\PromotionRequest;
use App\Http\Resources\AuctionPromotionFilterResource;
use App\Http\Resources\AuctionPromotionResource;
use App\Repositories\Interfaces\AuctionPromotionRepositoryInterface;
use Illuminate\Http\Request;

class AuctionPromotionController extends Controller
{
    public function __construct(protected AuctionPromotionRepositoryInterface $auctionPromotionRepository){}
    
    public function allPromotions(Request $request)
    {
        return successResponse('message.promotions',AuctionPromotionFilterResource::collection($this->auctionPromotionRepository->allPromotions($request->query('type'))) , 200);
    }

    public function myPromotions(Request $request)
    {
        return $this->auctionPromotionRepository->my($request->query('type'));
    }

    public function buyPackage(PromotionRequest $request)
    {
        $auctionPromotion = $this->auctionPromotionRepository->subcription($request->validated('package_id'), $request->validated('auction_id'), $request->validated('payment_type'))->refresh();
        if (!$auctionPromotion->relationLoaded('source') && is_null($auctionPromotion->source)) {
            $auctionPromotion->load('payable');
        }
        return successResponse('message.package_bought', AuctionPromotionResource::make($auctionPromotion), 200);
    }
}
