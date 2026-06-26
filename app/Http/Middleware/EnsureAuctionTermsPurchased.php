<?php

namespace App\Http\Middleware;

use App\Models\Auction;
use App\Models\AuctionTerm;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuctionTermsPurchased
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $auction = Auction::findOrFail($request->route('id'));
        // ?todo if terms price is 0
        if ($auction->terms_price == 0) {
            return $next($request);
        }

        // ?todo user should be buy the terms
        $hasPurchased = AuctionTerm::where('auction_id', $auction->id)
            ->where('user_id', auth()->id())
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'status' => false,
                'message' => __('message.auction_terms_not_purchased'),
            ], 403);
        }

        return $next($request);
    }
}
