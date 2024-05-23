<?php

use Spatie\FlareClient\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DefaultController;
use App\Http\Controllers\ExternalApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(DefaultController::class)->middleware('micro-service')->prefix('v1')->group(function () {
    Route::post('create-new-shop', 'createDefaultDetails');
    Route::post('delete-shop/{id}', 'deleteMerchantShop');
    Route::post('update-shop/{id}', 'updateMerchantShop');
    Route::get('admin-access-store', 'accessMerchantShop')->middleware('web');
});
