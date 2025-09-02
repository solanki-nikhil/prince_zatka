<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ListingController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\Api\WarrantyController;
use App\Http\Controllers\API\ProductApiController;
use App\Http\Controllers\Api\SerialNoController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\API\MediaMasterApiController;

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

Route::post('/register', [RegisterController::class, 'signup']);
Route::post('/login', [RegisterController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    Route::post('/warranty/check', [WarrantyController::class, 'checkWarranty']);
    Route::get('/serialno/rejected', [SerialNoController::class, 'rejectedList']);
    Route::put('/profile/update', [ProfileController::class, 'updateProfile']);
});

Route::get('/media', [MediaController::class, 'index']);
Route::middleware('auth:sanctum')->post('/logout', [RegisterController::class, 'logout']);


// Route::post('login', [LoginController::class, 'index']);

// Route::post('country-list', [ListingController::class, 'countryList']);
// Route::post('state-list', [ListingController::class, 'stateList']);
// Route::post('city-list', [ListingController::class, 'cityList']);

// Route::post('register', [RegisterController::class, 'store']);
// Route::post('otp-verify', [RegisterController::class, 'show']);
// Route::post('forget-password', [RegisterController::class, 'edit']);
// Route::post('reset-password', [RegisterController::class, 'update']);

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['middleware' => ['auth:sanctum']], function () {
//     Route::post('category-list', [ListingController::class, 'categoryList']);
//     Route::post('sub-category-list', [ListingController::class, 'subCategoryList']);
//     Route::post('status-list', [ListingController::class, 'statusList']);
//     Route::post('user-list', [ListingController::class, 'userList']);
    
//     //profile update
//     Route::post('user-profile-update', [ListingController::class, 'userProfile']);

//     Route::post('product-list', [ProductApiController::class, 'index']);
//     Route::post('product-add-update', [ProductApiController::class, 'store']);
//     Route::post('product-delete', [ProductApiController::class, 'destroy']);
//     Route::post('order-list', [OrderController::class, 'index']);
//     Route::post('order-add', [OrderController::class, 'store']);
//     Route::post('order-delete', [OrderController::class, 'destroy']);
//     Route::post('single-order-delete', [OrderController::class, 'removeOrder']);
    
//     //order status update
//     Route::post('order-status-update', [OrderController::class, 'update']);

//     Route::post('media-list', [MediaMasterApiController::class, 'index']);
//     Route::post('media-add-update', [MediaMasterApiController::class, 'store']);
//     Route::post('media-delete', [MediaMasterApiController::class, 'destroy']);

//     //redeem code
//     Route::post('redeem', [ListingController::class, 'redeem']);
//     //transection history
//     Route::post('transection', [ListingController::class, 'transection']);
    
//     Route::post('slider-list', [ListingController::class, 'sliderList']);
// });
