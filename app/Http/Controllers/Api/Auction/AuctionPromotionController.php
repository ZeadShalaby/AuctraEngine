<?php

namespace App\Http\Controllers\Api\Auction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auction\PromotionRequest;
use App\Http\Resources\AuctionPromotionResource;
use App\Repositories\Interfaces\AuctionPromotionRepositoryInterface;

class AuctionPromotionController extends Controller
{
    public function __construct(protected AuctionPromotionRepositoryInterface $auctionPromotionRepository)
    {
    }

    public function index($type)
    {
        return $this->auctionPromotionRepository->my($type);
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
