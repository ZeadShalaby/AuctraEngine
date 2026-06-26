<?php

namespace App\Http\Controllers\Api\Auction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auction\CreateRequest;
use App\Http\Requests\Auction\UpdateRequest;
use App\Http\Resources\AuctionResource;
use App\Http\Resources\CompleteAuctionPaymentResource;
use App\Http\Resources\Terms\BuyTermResource;
use App\Services\AuctionService;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function __construct(protected AuctionService $auctionService)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'category_id',
            'subcategory_id',
            'condition',
            'price_from',
            'price_to',
            'type',
            'sort',
        ]);
        return successResponse(
            'message.auctions_found',
            AuctionResource::collection(
                $this->auctionService->getAllAuctions(
                    $filters,
                    $request->integer('per_page', 15)
                )
            )
        );

    }

    public function myAuctions(Request $request)
    {
        $filters = $request->only([
            'category_id',
            'subcategory_id',
            'condition',
            'price_from',
            'price_to',
            'type',
            'sort',
        ]);
        return successResponse(
            'message.auctions_found',
            AuctionResource::collection(
                $this->auctionService->myAuctions(
                    $filters,
                    $request->integer('per_page', 15)
                )
            )
        );
    }


    public function create(CreateRequest $request)
    {
        return successResponse('message.auction_created', AuctionResource::make($this->auctionService->createAuction($request->validated())), 200);
    }

    public function show(int $id)
    {
        return successResponse('message.auction_found', BuyTermResource::make($this->auctionService->getAuction($id), 200));
    }

    public function update(UpdateRequest $request, int $id)
    {
        return successResponse('message.auction_created', AuctionResource::make($this->auctionService->updateAuction($id, $request->validated())), 200);
    }

    public function endedAuctions(int $auctionId)
    {
        $this->auctionService->endAuction($auctionId);
        return successResponse('message.auction_ended', AuctionResource::make($this->auctionService->getAuction($auctionId)), 200);
    }

    public function destroy(int $id)
    {
        $this->auctionService->deleteAuction($id);
        return successResponse('message.auction_deleted', null, 200);
    }
    // ?todo buy terms
    public function buyTerms($id)
    {
        return successResponse('message.auction_terms_buy_success', BuyTermResource::make($this->auctionService->buyTerms($id)), 200);
    }

    // ?todo placed bid
    public function placeBid(Request $request, int $auctionId)
    {
        $bid = $this->auctionService->placeBid(
            $auctionId,
            auth()->user(),
            $request->query('amount'),
            $request->query('max_auto_bid')
        );

        return successResponse('message.bid_placed', $bid, 200);
    }

    public function completeAuctionPayment(int $auctionId)
    {
        return successResponse('message.auction_payment_completed', CompleteAuctionPaymentResource::make($this->auctionService->completeAuctionPayment($auctionId)), 200);
    }
    
    public function bidHistory(int $auctionId)
    {
        return successResponse('message.bid_history', $this->auctionService->bidHistory($auctionId), 200);
    }

    //?todo winner
    public function myAuctionWinner()
    {
        return successResponse('message.highest_bid', AuctionResource::make($this->auctionService->myAuctionWinner()), 200);
    }

}