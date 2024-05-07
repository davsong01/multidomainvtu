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
    Route::get('delete-shop/{id}', 'deleteMerchantShop');
    
});

Route::controller(ExternalApiController::class)->prefix('v1')->group(function () {

    // Get all Categories
    Route::get('/category', 'getCategories');
    // Get all products
    Route::get('/products/{category_slug}', 'getProductsByCategory');
    // Get variations
    Route::get('/variations/{product_slug}', 'getVariationsByProductSlug');
    // Verify
    Route::post('/verify-biller', 'verifyBiller');

    Route::middleware('api-auth')->group(function () {
        // Get balance
        Route::get('/get-balance', 'getBalance');
        // Query
        Route::post('/query', 'makePayment');
        // Requery
        Route::post('/re-query', 'queryTransaction');
    });
});
