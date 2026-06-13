<?php

namespace App\Http\Controllers\Api\Auction;

use App\Http\Controllers\Controller;
use App\Services\AuctionService;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function __construct(protected AuctionService $auctionService) {}

    public function index()
    {
        return successResponse('message.auctions_retrieved',$this->auctionService->getAllAuctions(),200);
    }

    public function create(Request $request)
    {
        $auction = $this->auctionService->createAuction($request->all());

        return successResponse('message.auction_created',$auction, 200);
    }

    public function find(int $id)
    {
        return successResponse('message.auction_found',$this->auctionService->getAuction($id),200);
    }

    public function placeBid(Request $request, int $auctionId)
    {
        $bid = $this->auctionService->placeBid(
            $auctionId,
            auth()->user(),
            $request->amount,
            $request->max_auto_bid
        );

        return successResponse('message.bid_placed',$bid,200);
    }

    public function endAuction(int $auctionId)
    {
        $this->auctionService->endAuction($auctionId);
        return successResponse('message.auction_ended',null,200);
    }
}