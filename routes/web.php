<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaMasterController;
use App\Http\Controllers\PointRedeemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\StatusMasterController;
use App\Http\Controllers\StockMasterController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Models\PointRedeem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\SerialNOController;

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

Route::get('/', function () {
    return redirect()->route('login');
})->name('welcome');

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'Application cache has been cleared';
});
Route::get('/register', [RegisterController::class, 'index'])->name('register');

Route::post('/register', [RegisterController::class, 'register']);


Route::group(['middleware' => ['auth']], function () {
    
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::resource('user', UserProfileController::class);
    Route::post('user-change', [UserProfileController::class, 'userChange'])->name('user-change');
    Route::post('verify-password', [UserProfileController::class, 'verifyPassword'])->name('verify-password');

    Route::get('/profile', [UserProfileController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'updateProfile'])->name('profile.update');

    Route::resource('order', OrderController::class);
    Route::post('admin-single-order-delete', [OrderController::class, 'removeSingleOrder'])->name('admin-single-order-delete');
    Route::post('edit-single-order-delete', [OrderController::class, 'removeSingleItem'])->name('edit-single-order-delete');
    Route::get('generate-pdf/{id}', [OrderController::class, 'generatePDF'])->name('generate-pdf');

    Route::post('report-export', [HomeController::class, 'orderExport'])->name('report-export');
    Route::post('product-report-export', [HomeController::class, 'productExport'])->name('product-report-export');

    Route::resource('status-master', StatusMasterController::class);
    Route::resource('media-master', MediaMasterController::class);
    Route::resource('country', CountryController::class);
    Route::resource('state', StateController::class);
    Route::resource('city', CityController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('sub-category', SubCategoryController::class);
    Route::resource('product', ProductController::class);
    Route::post('product-change', [ProductController::class, 'productChange'])->name('product-change');

    Route::resource('stock-master', StockMasterController::class);
    Route::resource('qr-code', QRCodeController::class);
    Route::get('generate-qr/{id}', [QRCodeController::class, 'generateQR'])->name('generate-qr');

    Route::resource('redeem-point', PointRedeemController::class);
    Route::resource('slider', SliderController::class);
    
    Route::get('product/category/{id}', [ProductController::class, 'productFromCat'])->name('product.productFromCat');

    Route::prefix('serialno')->group(function () {
        Route::get('/rejected', [SerialNOController::class, 'rejected'])->name('serialno.rejected');
        Route::get('/replaced', [SerialNOController::class, 'replaced'])->name('serialno.replaced');
    });
    
    Route::resource('serialno', SerialNOController::class);
    

    Route::post('/serial/store', [SerialNOController::class, 'store'])->name('serial.store');
    Route::put('/serialno/update', [SerialNOController::class, 'update'])->name('serialno.update');
    Route::post('/serialno/uploadcsv', [SerialNoController::class, 'uploadCSVSerialNo'])->name('serialno.uploadcsvserialno');

    // Warranty Routes
    Route::get('/warranty/add', [SerialNOController::class, 'addWarranty'])->name('warranty.add');
    Route::post('/warranty/store', [SerialNOController::class, 'storeWarranty'])->name('warranty.store');

    Route::get('/get-categories-products', [SerialNOController::class, 'getCategoriesAndProducts'])->name('get.categories.products');

    // Push Notification Routes
    Route::prefix('api/push')->group(function () {
        Route::get('/vapid-public-key', function () {
            return response()->json([
                'publicKey' => config('services.vapid.public_key', 'your-vapid-public-key-here')
            ]);
        })->name('push.vapid-public-key');
        
        Route::post('/subscriptions', [App\Http\Controllers\PushSubscriptionController::class, 'store'])->name('push.subscriptions.store');
        Route::delete('/subscriptions', [App\Http\Controllers\PushSubscriptionController::class, 'destroy'])->name('push.subscriptions.destroy');
        Route::get('/subscriptions', [App\Http\Controllers\PushSubscriptionController::class, 'index'])->name('push.subscriptions.index');
    });

    // Route::get('/customer/dashboard', function () {
    //     return view('customer.dashboard');
    // })->name('customer.dashboard');

    Route::get('/get-products-by-category', [SerialNOController::class, 'getProductsByCategory'])->name('get.products.by.category');
    Route::get('/check-serial-number', [SerialNOController::class, 'checkSerialNumber'])->name('check.serial.number');
    Route::get('/check-save-serial-number', [SerialNOController::class, 'checkAndSaveSerialNumber'])->name('check.save.serial.number');
    Route::post('/place-replace-order', [OrderController::class, 'directOrderFromSerial'])->name('customerorder.replace');

    // Customer dashboard route
    Route::get('/customer/dashboard', [DashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/customer/order', [OrderController::class, 'customerview'])->name('customerorder');
    Route::put('/customer/order/{id}', [OrderController::class, 'customOrderUpdate'])->name('customer.order.update');
    Route::get('/customer/media', [MediaMasterController::class, 'customermedia'])->name('customermedia');
    

    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add')->middleware('auth');
    Route::post('/cart/update/{id}', [CartController::class, 'updateCart'])->name('cart.update')->middleware('auth');
    Route::post('/cart/remove', [CartController::class, 'removeItem'])->name('cart.remove')->middleware('auth');
    Route::post('/cart/manage', [CartController::class, 'manageCart'])->name('cart.manage')->middleware('auth');
    Route::get('/cart/view', [CartController::class, 'viewCart'])->name('cart.view');
    Route::get('/cart/total', [CartController::class, 'totalCart'])->name('cart.total');
    Route::post('/order/cartsubmit', [OrderController::class, 'cartsubmit'])->name('order.cartsubmit');
    
});
