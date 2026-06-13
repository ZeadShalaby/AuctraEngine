<?php

use App\Http\Controllers\Api\Reports\ReportsController;
use App\Http\Controllers\Api\Share\SharesController;
use App\Http\Controllers\Api\Auction\AuctionController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Categories\CategoriesController;
use App\Http\Controllers\Api\Comments\CommentsController;
use App\Http\Controllers\Api\Complaints\ComplaintsController;
use App\Http\Controllers\Api\Favourites\FavouritesController;
use App\Http\Controllers\Api\Interests\InterestsController;
use App\Http\Controllers\Api\Likes\LikesController;
use App\Http\Controllers\Api\Notifications\NotificationsController;
use App\Http\Controllers\Api\Posts\PostController;
use App\Http\Controllers\Api\Reels\ReelsController;
use App\Http\Controllers\Api\Reviews\ReviewsController;
use App\Http\Controllers\Api\Wallet\PaymentController;
use App\Services\FirebaseService;
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
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/change-avatar', [AuthController::class, 'changeAvatar']);
        Route::put('/update-profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
Route::middleware('jwt.auth:api')->group(function () {
    // ?todo posts
    Route::prefix('posts')->group(function () {
        Route::get('/all', [PostController::class, 'all']);
        Route::get('/{id}', [PostController::class, 'show']);
        Route::get('/search', [PostController::class, 'search']);
        Route::get('/user', [PostController::class, 'user']);
        Route::get('/my', [PostController::class, 'my']);
        Route::post('/create', [PostController::class, 'create']);
        Route::put('/update/{id}', [PostController::class, 'update']);
        Route::delete('/delete/{id}', [PostController::class, 'delete']);
    });

    // ?todo reels
    Route::prefix('reels')->group(function () {
        Route::get('/all', [ReelsController::class, 'allReels']);
    });
    // ?todo categories
    Route::prefix('categorys')->group(function () {
        Route::get('/all', [CategoriesController::class, 'all']);
        Route::get('/{id}', [CategoriesController::class, 'show']);
        Route::post('/create', [CategoriesController::class, 'create']);
        Route::put('/update/{id}', [CategoriesController::class, 'update']);
        Route::delete('/delete/{id}', [CategoriesController::class, 'delete']);
    });
    // ?todo interests
    Route::prefix('interests')->group(function () {
        Route::get('/my', [InterestsController::class, 'my']);
        Route::post('/toggle', [InterestsController::class, 'toggle']);
    });
    // ?todo reviews
    Route::prefix('reviews')->group(function () {
        Route::get('/seller/avg/rating/{id}', [ReviewsController::class, 'sellerAverageRating']);
        Route::get('/seller/reviews/count/{id}', [ReviewsController::class, 'sellerReviewsCount']);
        Route::get('/seller/reviews/{id}', [ReviewsController::class, 'sellerReviews']);
        Route::get('/buyer/reviews/{id}', [ReviewsController::class, 'buyerReviews']);
        Route::get('/{id}', [ReviewsController::class, 'show']);
        Route::post('/create', [ReviewsController::class, 'create']);
        Route::put('/update/{id}', [ReviewsController::class, 'update']);
        Route::delete('/delete/{id}', [ReviewsController::class, 'delete']);
    });

    // ?todo Complaints
    Route::prefix('complaints')->group(function () {
        Route::get('/my', [ComplaintsController::class, 'my']);
        Route::post('/create', [ComplaintsController::class, 'create']);
    });

    // ?todo Comments
    Route::prefix('comments')->group(function () {
        Route::get('/all', [CommentsController::class, 'all']);
        Route::get('/{id}', [CommentsController::class, 'show']);
        Route::post('/create', [CommentsController::class, 'create']);
        Route::put('/update/{id}', [CommentsController::class, 'update']);
        Route::delete('/delete/{id}', [CommentsController::class, 'delete']);
    });

    // ?todo Favourites
    Route::prefix('favourites')->group(function () {
        Route::get('/toggle', [FavouritesController::class, 'toggle']);
    });

    // ?todo likes
    Route::prefix('likes')->group(function () {
        Route::get('/toggle', [LikesController::class, 'toggle']);
        Route::get('/content', [LikesController::class, 'content']);
    });

    // ?todo shares
    Route::prefix('shares')->group(function () {
        Route::get('/my', [SharesController::class, 'my']);
        Route::get('/toggle', [SharesController::class, 'toggle']);
    });

    // ?todo reports
    Route::prefix('reports')->group(function () {
        Route::post('/create', [ReportsController::class, 'create']);
    });

    // ?todo notifications
    Route::prefix('notifications')->group(function () {
        Route::post('/save-fcm-token', [NotificationsController::class, 'saveFcmToken']);
        Route::get('/my', [NotificationsController::class, 'my']);
        Route::get('/unread', [NotificationsController::class, 'unread']);
        Route::put('/read/{id}', [NotificationsController::class, 'read']);
        Route::put('/read-all', [NotificationsController::class, 'readAll']);
        Route::delete('/delete/{id}', [NotificationsController::class, 'delete']);
        Route::post('/send/all', [NotificationsController::class, 'sendToAll']);
        Route::post('/send', [NotificationsController::class, 'send']);
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




Route::post('/payment/create', [PaymentController::class, 'create']);
Route::get('/pay/{ref}', [PaymentController::class, 'showPaymentPage']);
// ? callback URL that Moamalat will call after payment
Route::get('/success', fn() => "Payment Success");
Route::get('/fail', fn() => "Payment Failed");
Route::get('/cancel', fn() => "Payment Cancelled");

