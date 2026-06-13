<?php

// Controllers
use App\Http\Controllers\Api\Wallet\PaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\UserController;
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

//Landing-Pages Routes
// Route::group(['prefix' => 'landing-pages'], function () {
//     Route::get('index', [HomeController::class, 'landing_index'])->name('landing-pages.index');
//     });


Route::group(['middleware' => 'auth'], function () {
    // Permission Module
    Route::get('/role-permission', [RolePermission::class, 'index'])->name('role.permission.list');
    Route::resource('permission', PermissionController::class);
    Route::resource('role', RoleController::class);

    // Dashboard Routes
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Users Module
    Route::resource('users', UserController::class);

    Route::prefix('users')->name('users.')->group(function () {

        Route::post('{user}/toggle', [UserController::class, 'toggle'])->name('toggle');
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