<?php

use App\Http\Controllers\Api\Ads\AdsController;
use App\Http\Controllers\Api\Ads\AdsPrices\AdPriceController;
use App\Http\Controllers\Api\Auction\AuctionController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Categories\CategoriesController;
use App\Http\Controllers\Api\Categories\SubCategories\SubCategoriesController;
use App\Http\Controllers\Api\Comments\CommentsController;
use App\Http\Controllers\Api\Complaints\ComplaintsController;
use App\Http\Controllers\Api\Favourites\FavouritesController;
use App\Http\Controllers\Api\Interests\InterestsController;
use App\Http\Controllers\Api\Likes\LikesController;
use App\Http\Controllers\Api\Notifications\NotificationsController;
use App\Http\Controllers\Api\Posts\PostController;
use App\Http\Controllers\Api\Reels\ReelsController;
use App\Http\Controllers\Api\Reports\ReportsController;
use App\Http\Controllers\Api\Reviews\ReviewsController;
use App\Http\Controllers\Api\Share\SharesController;
use App\Http\Controllers\Api\Wallet\PaymentController;
use App\Http\Controllers\Api\Wallet\TransactionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'auth', 'middleware' => ['setLocale', 'throttle:api']], function () {
    // ?todo auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:otp');
    Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:otp');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::group(['middleware' => 'jwt.auth:api', 'verified.custom'], function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/user-profile/{id}', [AuthController::class, 'userProfile']);
        Route::post('/complete-profile', [AuthController::class, 'completeProfile']);
        // Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/change-avatar', [AuthController::class, 'changeAvatar']);
        Route::put('/update-profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
Route::group(['middleware' => ['jwt.auth:api', 'verified.custom', 'setLocale', 'throttle:api']], function () {

    // ?todo categories
    Route::prefix('categorys')->group(function () {
        Route::get('/all', [CategoriesController::class, 'index']);
        Route::get('/{id}', [CategoriesController::class, 'show']);

        Route::prefix('sub')->group(function () {
            Route::get('/all', [SubCategoriesController::class, 'index']);
            Route::get('/{id}', [SubCategoriesController::class, 'show']);
        });

    });

    // ?todo interests
    Route::prefix('interests')->group(function () {
        Route::get('/my', [InterestsController::class, 'index']);
        Route::post('/toggle/{category}', [InterestsController::class, 'toggle']);
    });

    // ?todo posts
    Route::prefix('posts')->group(function () {
        Route::get('/all', [PostController::class, 'all']);
        Route::get('/{id}', [PostController::class, 'show'])->where('id', '[0-9]+');
        Route::get('/search/{query}', [PostController::class, 'search']);
        Route::get('/user/{user_id}', [PostController::class, 'userPosts']);
        Route::get('/my', [PostController::class, 'myPosts']);
        Route::post('/create', [PostController::class, 'create']);
        Route::post('/update/{id}', [PostController::class, 'update']);
        Route::delete('/delete/{id}', [PostController::class, 'delete']);
    });

    // ?todo reels
    Route::prefix('reels')->group(function () {
        Route::get('/all', [ReelsController::class, 'index']); // ? all reels
        Route::get('/feed', [ReelsController::class, 'feed']); // ? For You Feed (its return the recommended reels in persantage 80/20)
        Route::get('/{id}', [ReelsController::class, 'show'])->where('id', '[0-9]+');
        Route::post('/search', [ReelsController::class, 'search']);
        Route::get('/user/{user_id}', [ReelsController::class, 'userReels']);
        Route::get('/my', [ReelsController::class, 'myReels']);
        Route::post('/create', [ReelsController::class, 'create']);
        Route::put('/update/{id}', [ReelsController::class, 'update']);
        Route::delete('/delete/{id}', [ReelsController::class, 'delete']);
    });
    // ?todo reviews
    Route::prefix('reviews')->group(function () {
        // Route::get('/seller/avg/rating/{id}', [ReviewsController::class, 'sellerAverageRating']);
        // Route::get('/seller/reviews/count/{id}', [ReviewsController::class, 'sellerReviewsCount']);
        Route::post('/seller/{id}', [ReviewsController::class, 'sellerReviews']);
        Route::post('/reviewer', [ReviewsController::class, 'reviewerReviews']);
        Route::get('/{id}', [ReviewsController::class, 'show']);
        Route::post('/create', [ReviewsController::class, 'store']);
        Route::put('/update/{id}', [ReviewsController::class, 'update']);
        Route::delete('/delete/{id}', [ReviewsController::class, 'destroy']);
    });

    // ?todo Complaints
    Route::prefix('complaints')->group(function () {
        Route::get('/my', [ComplaintsController::class, 'index']);
        Route::post('/create', [ComplaintsController::class, 'store']);
    });

    // ?todo reports
    Route::prefix('reports')->group(function () {
        Route::post('/create', [ReportsController::class, 'store']);
    });

    // ?todo Comments
    Route::prefix('comments')->group(function () {
        Route::post('/all', [CommentsController::class, 'index']);
        Route::get('/{id}', [CommentsController::class, 'show']);
        Route::post('/create', [CommentsController::class, 'store']);
        Route::post('/update/{id}', [CommentsController::class, 'update']);
        Route::delete('/delete/{id}', [CommentsController::class, 'destroy']);
    });

    // ?todo Favourites
    Route::prefix('favourites')->group(function () {
        Route::post('/toggle', [FavouritesController::class, 'toggle']);
        Route::get('/my', [FavouritesController::class, 'myFavourites']);
    });

    // ?todo likes
    Route::prefix('likes')->group(function () {
        Route::post('/toggle', [LikesController::class, 'toggle']);
        Route::post('/content', [LikesController::class, 'getContent']);
    });

    // ?todo shares
    Route::prefix('shares')->group(function () {
        Route::post('/toggle', [SharesController::class, 'toggle']);
        Route::get('/my', [SharesController::class, 'index']);
        Route::get('/whos/{shareable_type}/{shareable_id}', [SharesController::class, 'getSharedUsers']);
    });

    // ?todo notifications
    Route::prefix('notifications')->group(function () {
        Route::post('/save-fcm-token', [NotificationsController::class, 'saveFcmToken']);
        Route::get('/my', [NotificationsController::class, 'getNotifications']);
        Route::get('/unread', [NotificationsController::class, 'getUnreadNotifications']);
        Route::put('/read/{id}', [NotificationsController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationsController::class, 'markAllAsRead']);
        // Route::post('/send/all', [NotificationsController::class, 'sendToAll']);
        // Route::post('/send', [NotificationsController::class, 'send']);
    });

    // ?todo transactions 
    Route::prefix('transactions')->group(function () {
        Route::get('/my', [TransactionsController::class, 'myTransactions']);
        Route::get('/{id}', [TransactionsController::class, 'show']);
    });
});

// ?todo auction
Route::prefix('auctions')->group(function () {
    //? public
    Route::get('/all', [AuctionController::class, 'allAuctions']);
    Route::get('/search', [AuctionController::class, 'searchAuctions']);
    Route::get('/{id}', [AuctionController::class, 'showAuction']);
    Route::get('/active', [AuctionController::class, 'activeAuctions']);
    Route::get('/upcoming', [AuctionController::class, 'upcomingAuctions']);
    Route::get('/ended', [AuctionController::class, 'endedAuctions']);

    //? auth required
    Route::middleware('jwt.auth:api')->group(function () {

        Route::get('/watchlist', [AuctionController::class, 'userWatchlist']);
        Route::post('/watchlist/add', [AuctionController::class, 'addToWatchlist']);

        Route::get('/bid-history/{auctionId}', [AuctionController::class, 'bidHistory']);
        Route::get('/highest-bid/{auctionId}', [AuctionController::class, 'highestBid']);

        //? verified required
        Route::middleware('verified.custom')->group(function () {
            Route::post('/bid', [AuctionController::class, 'placeBid']);
        });
    });
});

// ?todo Ads
Route::prefix('ads')->middleware('jwt.auth:api')->group(function () {
    Route::get('/all', [AdsController::class, 'index']);
    Route::get('/{id}', [AdsController::class, 'show']);
    Route::post('/create', [AdsController::class, 'create']);
    Route::post('/update/{id}', [AdsController::class, 'update']);
    Route::delete('/delete/{id}', [AdsController::class, 'destroy']);
    Route::post('/payments/callback', [AdsController::class, 'callback'])->name('ads.callback');
    // ?todo Ads price
    Route::prefix('price')->group(function () {
        Route::get('/all', [AdPriceController::class, 'index']);
        Route::get('/{id}', [AdPriceController::class, 'show']);
    });
});


Route::post('/payment/create', [PaymentController::class, 'create']);
Route::post('/pay/{ref}', [PaymentController::class, 'showPaymentPage']);
// ? callback URL that Moamalat will call after payment
Route::any('/success', fn() => view('payment.success_page'))->name('payment.success');
Route::any('/fail', fn() => "Payment Failed")->name('payment.failed');
Route::get('/cancel', fn() => "Payment Cancelled");

