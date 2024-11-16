<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Admin\Auth\AdminAuthController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Products\ProductController;

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
// ------------------------------------------------ADMIN API
Route::group(['prefix' => 'admin'], function () {
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('login', [AdminAuthController::class, 'login']);
});
// ------------------------------------------------ADMIN API

// ------------------------------------------------USER API
Route::group(['prefix' => 'user'], function () {
    Route::post('send-otp', 'User\Auth\UserAuthController@sendOTP');
    Route::post('validate-otp', 'User\Auth\UserAuthController@validateOTP');
    Route::post('send-otp-login', 'User\Auth\UserAuthController@sendOTPForLogin');
    Route::post('validate-otp-login', 'User\Auth\UserAuthController@validateOTPForLogin');
    Route::post('register', 'User\Auth\UserAuthController@register');
    Route::post('login', 'User\Auth\UserAuthController@login');
    Route::post('send-reset-otp', 'User\Auth\UserAuthController@sendResetOtp')->name('password.reset');
    Route::post('validate-otp-reset-password', 'User\Auth\UserAuthController@validateOtpAndResetPassword');
    // Route::get('user-update', 'User\Auth\UserAuthController@updateSpecialId');
    // Route::post('/save-fcm-token', 'User\Auth\UserAuthController@storeFcmToken');
});
// ------------------------------------------------USER API

// ------------------------------------------------USER API
Route::group(['prefix' => 'team-user'], function () {
    Route::post('send-otp', 'TeamUser\TeamUserAuthController@sendOTP');
    Route::post('validate-otp', 'TeamUser\TeamUserAuthController@validateOTP');
    Route::post('send-otp-login', 'TeamUser\TeamUserAuthController@sendOTPForLogin');
    Route::post('validate-otp-login', 'TeamUser\TeamUserAuthController@validateOTPForLogin');
    Route::post('register', 'TeamUser\TeamUserAuthController@register');
    Route::post('login', 'TeamUser\TeamUserAuthController@login');
    // Route::post('send-reset-otp', 'TeamUser\TeamUserAuthController@sendResetOtp')->name('password.reset');
    Route::post('validate-otp-reset-password', 'TeamUser\TeamUserAuthController@validateOtpAndResetPassword');
    // Route::get('user-update', 'TeamUser\TeamUserAuthController@updateSpecialId');
});
// ------------------------------------------------USER API

// ------------------------------------------------PRODUCT API
Route::group(['prefix' => 'product'], function () {
    Route::get('product-search-barcode', [ProductController::class, 'productCode']);
});
// ------------------------------------------------PRODUCT API
