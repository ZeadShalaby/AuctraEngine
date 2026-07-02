<?php

// Controllers
use App\Http\Controllers\Api\Wallet\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\UserController;
use App\Http\Controllers\web\Ads\AdsController;
use App\Http\Controllers\web\Ads\AdsPriceController;
use App\Http\Controllers\web\Auctions\AuctionPromotionController;
use App\Http\Controllers\web\Auctions\AuctionsController;
use App\Http\Controllers\web\Auctions\AuctionTermController;
use App\Http\Controllers\web\Auctions\AuctionWatcherController;
use App\Http\Controllers\web\Auctions\PromotionPackageController;
use App\Http\Controllers\web\Bids\BidsController;
use App\Http\Controllers\web\Cards\CardController;
use App\Http\Controllers\web\Cards\RechargeController;
use App\Http\Controllers\web\Category\CategoriesController;
use App\Http\Controllers\web\Category\SubCategoriesController;
use App\Http\Controllers\web\Companys\CompanyController;
use App\Http\Controllers\web\Complaint\ComplaintsController;
use App\Http\Controllers\web\Notifications\NotificationsController;
use App\Http\Controllers\web\Reports\ReportsActionController;
use App\Http\Controllers\web\Reports\ReportsController;
use App\Http\Controllers\web\Wallet\PaymentsController;
use App\Http\Controllers\web\Wallet\TransactionsController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::get('/storage', function () {
    Artisan::call('storage:link');
});

Route::get('/', [HomeController::class, 'landing_index'])->name('landing-pages.index');


Route::group(['middleware' => 'auth'], function () {
    // ?todo Permission Module
    Route::get('/role-permission', [RolePermission::class, 'index'])->name('role.permission.list');
    Route::resource('permission', PermissionController::class);
    Route::resource('role', RoleController::class);

    // ?todo Dashboard Routes
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // ?todo Users Module
    Route::resource('users', UserController::class);

    Route::prefix('users')->name('users.')->group(function () {
        Route::post('{user}/toggle', [UserController::class, 'toggle'])->name('toggle');
    });
    // ?todo categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('categories.all');
        Route::get('/{id}', [CategoriesController::class, 'show'])->name('categories.show')->where('id', '[0-9]+');
        Route::get('/edit/{id}', [CategoriesController::class, 'edit'])->name('categories.edit');
        Route::post('/create', [CategoriesController::class, 'create'])->name('categories.create');
        Route::delete('/delete/{id}', [CategoriesController::class, 'delete'])->name('categories.destroy');
        Route::post('/update/{id}', [CategoriesController::class, 'update'])->name('categories.update');

        // ?TODO SUB-CATEGORIES
        Route::prefix('sub')->group(function () {
            Route::get('/', [SubCategoriesController::class, 'index'])->name('subcategories.all');
            Route::get('/{id}', [SubCategoriesController::class, 'show'])->name('subcategories.show')->where('id', '[0-9]+');
            Route::get('/edit/{id}', [SubCategoriesController::class, 'edit'])->name('subcategories.edit');
            Route::post('/create', [SubCategoriesController::class, 'create'])->name('subcategories.create');
            Route::delete('/delete/{id}', [SubCategoriesController::class, 'delete'])->name('subcategories.destroy');
            Route::post('/update/{id}', [SubCategoriesController::class, 'update'])->name('subcategories.update');
        });
    });

    // ?todo Auctions
    Route::prefix('auctions')->group(function () {
        Route::get('/', [AuctionsController::class, 'index'])->name('auctions.all');
        Route::get('/{id}', [AuctionsController::class, 'show'])->name('auctions.show')->where('id', '[0-9]+');
        Route::delete('/delete/{id}', [AuctionsController::class, 'delete'])->name('auctions.destroy');
        // ?todo packages
        Route::prefix('packages')->group(function () {
            Route::get('/', [PromotionPackageController::class, 'index'])->name('packages.all');
            Route::get('/{id}', [PromotionPackageController::class, 'show'])->name('packages.show')->where('id', '[0-9]+');
            Route::get('/edit/{id}', [PromotionPackageController::class, 'edit'])->name('packages.edit');
            Route::post('/create', [PromotionPackageController::class, 'create'])->name('packages.create');
            Route::delete('/delete/{id}', [PromotionPackageController::class, 'delete'])->name('packages.destroy');
            Route::post('/update/{id}', [PromotionPackageController::class, 'update'])->name('packages.update');
        });
        // ?todo Promotions
        Route::prefix('promotions')->group(function () {
            Route::get('/', [AuctionPromotionController::class, 'index'])->name('promotions.all');
            Route::get('/{id}', [AuctionPromotionController::class, 'show'])->name('promotions.show')->where('id', '[0-9]+');
            Route::delete('/delete/{id}', [AuctionPromotionController::class, 'delete'])->name('promotions.destroy');
        });
        // ?todo Watchers
        Route::prefix('watchers')->group(function () {
            Route::get('/', [AuctionWatcherController::class, 'index'])->name('watchers.all');
            Route::get('/{id}', [AuctionWatcherController::class, 'show'])->name('watchers.show')->where('id', '[0-9]+');
        });
        // ?todo Terms
        Route::prefix('terms')->group(function () {
            Route::get('/', [AuctionTermController::class, 'index'])->name('terms.all');
            Route::get('/{id}', [AuctionTermController::class, 'show'])->name('terms.show')->where('id', '[0-9]+');
            Route::delete('/delete/{id}', [AuctionTermController::class, 'delete'])->name('terms.destroy');
        });
        // ?todo Bids
        Route::prefix('bids')->group(function () {
            Route::get('/', [BidsController::class, 'index'])->name('bids.all');
            Route::get('/{id}', [BidsController::class, 'show'])->name('bids.show')->where('id', '[0-9]+');
        });
    });

    // ?todo Ads
    Route::prefix('ads')->group(function () {
        Route::get('/', [AdsController::class, 'index'])->name('ads.all');
        Route::get('/{id}', [AdsController::class, 'show'])->name('ads.show')->where('id', '[0-9]+');
        Route::delete('/delete/{id}', [AdsController::class, 'delete'])->name('ads.destroy');

        Route::prefix('price')->group(function () {
            Route::get('/', [AdsPriceController::class, 'index'])->name('price.all');
            Route::get('/{id}', [AdsPriceController::class, 'show'])->name('price.show')->where('id', '[0-9]+');
            Route::get('/edit/{id}', [AdsPriceController::class, 'edit'])->name('price.edit');
            Route::post('/create', [AdsPriceController::class, 'create'])->name('price.create');
            Route::delete('/delete/{id}', [AdsPriceController::class, 'delete'])->name('price.destroy');
            Route::post('/update/{id}', [AdsPriceController::class, 'update'])->name('price.update');
            Route::post('/{id}/toggle', [AdsPriceController::class, 'toggle'])->name('price.toggle');
        });
    });

    // ?todo complaint
    Route::prefix('complaints')->group(function () {
        Route::get('/', [ComplaintsController::class, 'index'])->name('complaints.all');
        Route::get('/{id}', [ComplaintsController::class, 'show'])->name('complaints.show')->where('id', '[0-9]+');
        Route::delete('/delete/{id}', [ComplaintsController::class, 'delete'])->name('complaints.destroy');
        Route::post('/{id}/toggle', [ComplaintsController::class, 'toggle'])->name('complaints.toggle');
    });

    // ?todo Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('reports.all');
        Route::get('/{id}', [ReportsController::class, 'show'])->name('reports.show')->where('id', '[0-9]+');
        Route::delete('/delete/{id}', [ReportsController::class, 'delete'])->name('reports.destroy');
        Route::post('/{id}/toggle', [ReportsController::class, 'toggle'])->name('reports.toggle');

        Route::prefix('actions')->group(function () {
            Route::get('/', [ReportsActionController::class, 'index'])->name('actions.all');
            Route::get('/{id}', [ReportsActionController::class, 'show'])->name('actions.show')->where('id', '[0-9]+');
        });
    });

    // ?todo company
    Route::prefix('companys')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('companys.all');
        Route::get('/{id}', [CompanyController::class, 'show'])->name('companys.show')->where('id', '[0-9]+');
        Route::get('/edit/{id}', [CompanyController::class, 'edit'])->name('companys.edit');
        Route::post('/create', [CompanyController::class, 'create'])->name('companys.create');
        Route::delete('/delete/{id}', [CompanyController::class, 'delete'])->name('companys.destroy');
        Route::post('/update/{id}', [CompanyController::class, 'update'])->name('companys.update');
        Route::post('/{id}/toggle', [CompanyController::class, 'toggle'])->name('companys.toggle');
    });

    // ?todo cards
    Route::prefix('cards')->group(function () {
        Route::get('/', [CardController::class, 'index'])->name('cards.all');
        Route::get('/{id}', [CardController::class, 'show'])->name('cards.show')->where('id', '[0-9]+');
        Route::get('/edit/{id}', [CardController::class, 'edit'])->name('cards.edit');
        Route::post('/create', [CardController::class, 'create'])->name('cards.create');
        Route::delete('/delete/{id}', [CardController::class, 'delete'])->name('cards.destroy');
        Route::post('/update/{id}', [CardController::class, 'update'])->name('cards.update');
        Route::post('/{id}/toggle', [CardController::class, 'toggle'])->name('cards.toggle');
        Route::post('/import', [CardController::class, 'import'])->name('cards.import');
        Route::post('export', [CardController::class, 'export'])->name('cards.export');
        // ?todo recharges
        Route::prefix('recharges')->group(function () {
            Route::get('/', [RechargeController::class, 'index'])->name('recharges.all');
            Route::get('/{id}', [RechargeController::class, 'show'])->name('recharges.show')->where('id', '[0-9]+');
            Route::get('/edit/{id}', [RechargeController::class, 'edit'])->name('recharges.edit');
            Route::post('/create', [RechargeController::class, 'create'])->name('recharges.create');
            Route::delete('/delete/{id}', [RechargeController::class, 'delete'])->name('recharges.destroy');
            Route::post('/update/{id}', [RechargeController::class, 'update'])->name('recharges.update');
            Route::post('/import', [RechargeController::class, 'import'])->name('recharges.import');
            Route::post('export', [RechargeController::class, 'export'])->name('recharges.export');
        });
    });
    // ?todo notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationsController::class, 'index'])->name('notifications.all');
        Route::get('/{id}', [NotificationsController::class, 'show'])->name('notifications.show')->where('id', '[0-9]+');
        Route::post('/read/{id}', [NotificationsController::class, 'read'])->name('notifications.read');
        Route::post('/read-all', [NotificationsController::class, 'readAll'])->name('notifications.readAll');
    });

    // ?todo wallet
    Route::prefix('wallet')->group(function () {
        // ?TODO PAYMENTS
        Route::prefix('payments')->group(function () {
            Route::get('/', [PaymentsController::class, 'index'])->name('payments.all');
            Route::get('/{id}', [PaymentsController::class, 'show'])->name('payments.show')->where('id', '[0-9]+');
        });
        // ?todo transactions
        Route::prefix('transactions')->group(function () {
            Route::get('/', [TransactionsController::class, 'index'])->name('transactions.all');
            Route::get('/{id}', [TransactionsController::class, 'show'])->name('transactions.show')->where('id', '[0-9]+');
        });
    });
});


//Widget Routs
Route::group(['prefix' => 'widget'], function () {
    Route::get('widget-basic', [HomeController::class, 'widgetbasic'])->name('widget.widgetbasic');
    Route::get('widget-chart', [HomeController::class, 'widgetchart'])->name('widget.widgetchart');
    Route::get('widget-card', [HomeController::class, 'widgetcard'])->name('widget.widgetcard');
});

//Maps Routs
Route::group(['prefix' => 'maps'], function () {
    Route::get('google', [HomeController::class, 'google'])->name('maps.google');
    Route::get('vector', [HomeController::class, 'vector'])->name('maps.vector');
});

//Auth pages Routs
Route::group(['prefix' => 'auth'], function () {
    Route::get('signin', [HomeController::class, 'signin'])->name('auth.signin');
    Route::get('signup', [HomeController::class, 'signup'])->name('auth.signup');
    Route::get('confirmmail', [HomeController::class, 'confirmmail'])->name('auth.confirmmail');
    Route::get('lockscreen', [HomeController::class, 'lockscreen'])->name('auth.lockscreen');
    Route::get('recoverpw', [HomeController::class, 'recoverpw'])->name('auth.recoverpw');
    Route::get('userprivacysetting', [HomeController::class, 'userprivacysetting'])->name('auth.userprivacysetting');
});

//Error Page Route
Route::group(['prefix' => 'errors'], function () {
    Route::get('error404', [HomeController::class, 'error404'])->name('errors.error404');
    Route::get('error500', [HomeController::class, 'error500'])->name('errors.error500');
    Route::get('maintenance', [HomeController::class, 'maintenance'])->name('errors.maintenance');
});


//Settings Pages Routs
Route::group(['prefix' => 'settings'], function () {
    Route::get('element', [HomeController::class, 'element'])->name('settings.element');
    Route::get('wizard', [HomeController::class, 'wizard'])->name('settings.wizard');
    Route::get('validation', [HomeController::class, 'validation'])->name('settings.validation');
});


//Table Page Routs
Route::group(['prefix' => 'table'], function () {
    Route::get('bootstraptable', [HomeController::class, 'bootstraptable'])->name('table.bootstraptable');
    Route::get('datatable', [HomeController::class, 'datatable'])->name('table.datatable');
});

//Extra Page Routs
Route::get('privacy-policy', [HomeController::class, 'privacypolicy'])->name('pages.privacy-policy');
Route::get('terms-of-use', [HomeController::class, 'termsofuse'])->name('pages.term-of-use');




Route::post('/payment/create', [PaymentController::class, 'create']);
Route::get('/pay/{ref}', [PaymentController::class, 'showPaymentPage']);
// ? callback URL that Moamalat will call after payment
Route::get('/success', fn() => "Payment Success");
Route::get('/fail', fn() => "Payment Failed");
Route::get('/cancel', fn() => "Payment Cancelled");