<?php

use App\Http\Controllers\Admins\Auth\LoginController as AuthLoginController;
use App\Http\Controllers\Admins\Category\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admins\Order\OrderController as AdminOrderController;
use App\Http\Controllers\Admins\Product\ProductController as AdminProductController;
use App\Http\Controllers\Admins\Users\UserController as AdminUserController;
use App\Http\Controllers\Admins\Product\ProductVariantController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Users\Addresses\AddressController;
use App\Http\Controllers\Users\Addresses\AddressDataController;
use App\Http\Controllers\Users\Products\ProductController;
use App\Http\Controllers\Users\Auth\LoginController;
use App\Http\Controllers\Users\Auth\MeController;
use App\Http\Controllers\Users\Auth\PasswordResetController;
use App\Http\Controllers\Users\Auth\RegisterController;
use App\Http\Controllers\Users\Carts\CartController;
use App\Http\Controllers\Users\Categories\CategoryController;
use App\Http\Controllers\Users\Orders\MoyasarWebhookController;
use App\Http\Controllers\Users\Orders\OrderController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:users')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [RegisterController::class, 'action']);
    Route::post('login', [LoginController::class, 'action']);
    Route::group(['middleware' => 'auth:users'], function () {
        Route::get('me', [MeController::class, 'action']);
        Route::post('/email/send-verify', [RegisterController::class, 'sendVerificationEmail']);
    });
    Route::get('/email/verify/{id}/{hash}', [RegisterController::class, 'verifyMail'])->middleware(['signed'])->name('api.verification.verify');
    Route::post('/password/reset-request', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
});

Route::get('categories', [CategoryController::class, 'index']);
Route::apiResource('products', ProductController::class);
Route::get('/images/{path}', [ImageController::class, 'get'])->where('path', '.*');

Route::apiResource('cart', CartController::class, [
    'parameters' => [
        'cart' => 'productVariation'
    ]
]);

Route::apiResource('orders', OrderController::class)->middleware('auth:users');
Route::get('orders/validate/{order}', [OrderController::class, 'validateOrderPayment'])->middleware('auth:users');
Route::post('orders/pay-with-cash', [OrderController::class, 'payWithCash'])->middleware('auth:users')->middleware('userIsBusiness');


Route::apiResource('addresses', AddressController::class);
Route::get('cities', [AddressDataController::class, 'cities']);
Route::get('provinces', [AddressDataController::class, 'provinces']);


Route::group(['prefix' => 'management'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthLoginController::class, 'action']);
    });

    Route::group(['middleware' => 'auth:admins'], function () {
        Route::apiResource('categories', AdminCategoryController::class);
        Route::post('categories/{category}', [AdminCategoryController::class, 'update']);
        Route::apiResource('products', AdminProductController::class);
        Route::apiResource('product-variants', ProductVariantController::class);
        Route::apiResource('orders', AdminOrderController::class);
        Route::post('/update-variant-stock/{product_variant}', [ProductVariantController::class, 'updateVariantStock']);
        Route::post('product-variants/create/{productId}', [ProductVariantController::class, 'create']);
        Route::post('product-variants/add-wholesale-prices/{product_variant}', [ProductVariantController::class, 'addWholeSalePricesToProductVariant']);
        Route::put('product-variants/update-wholesale-prices/{product_variant}', [ProductVariantController::class, 'updateWholeSalePricesToProductVariant']);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users/toggle-business/{user}', [AdminUserController::class, 'toggleIsBusiness']);
    });
});

Route::post('/moyasar-webhook', [MoyasarWebhookController::class,'action']);

Route::get('/set-verified-false', function(Request $request){
    User::where('email', $request->email)->update([
        'email_verified_at' => null
    ]);
});