<?php

use App\Http\Controllers\Web\FetchController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::group(['middleware' => [SetLocale::class]], function () {
    Route::get('/', [FetchController::class, 'index'])->name('home');
    Route::post('/fetch', [FetchController::class, 'fetch'])->name('fetch');
});

Route::get('/lang/{lang}', function($lang){
    if(in_array($lang, ['en','ar'])){
        session(['locale' => $lang]);
    }
    return redirect()->back();
});