<?php

use App\Http\Livewire\Home\Home;
use App\Models\TeamUserPermission;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Home\Auth\Login;
use App\Http\Livewire\Home\Auth\Landing;
use App\Http\Livewire\Home\Auth\Page;
use App\Http\Livewire\Home\Auth\OtpLogin;
use App\Http\Livewire\Home\Auth\Register;
use App\Http\Livewire\Home\Auth\TeamMember;
use App\Http\Livewire\Home\Auth\AdminLogin;
use App\Http\Livewire\Home\Auth\Contactless;
use App\Http\Livewire\Home\Auth\PaperlessProof;
use App\Http\Livewire\Home\Auth\ForgotPassword;
use App\Http\Controllers\DataTransferController;
use App\Http\Livewire\Home\Auth\ForgotPasswordOtp;
use App\Http\Livewire\Home\Auth\OtpConfirmation;
use App\Http\Livewire\Home\Auth\ChangePassword;
use App\Http\Controllers\V1\Profile\ProfileController;
use App\Http\Livewire\AcceptRejectChallanWithOtp\AcceptRejectChallanWithOtp;
use App\Http\Livewire\AcceptRejectDocumentWithOtp\AcceptRejectDocumentWithOtp;

// Route::get('accept-challan/{challanId}/{action}', AcceptRejectChallanWithOtp::class)->name('accept.challan');
Route::get('accept-challan/{challanId}', AcceptRejectChallanWithOtp::class)->name('accept.challan');

Route::get('accept-document/{type}/{documentId}', AcceptRejectDocumentWithOtp::class)->name('accept.document');

Route::get('/', function () {
    return view('user.home.index');
})->name('home');

Route::get('/landing-page', Landing::class)
    ->name('landing-page');

Route::get('/paperless-proof', PaperlessProof::class)
    ->name('paperless-proof');

Route::get('/contactless', Contactless::class)
    ->name('contactless');

Route::get('/login', Login::class)
    ->name('login');

    Route::get('/team-member', TeamMember::class)
        ->name('teamlogin');

Route::get('/register', Register::class)
    ->name('register');

Route::get('/otp-login', OtpLogin::class)
    ->name('otplogin');

Route::get('/otp-confirmation', OtpConfirmation::class)
    ->name('otpconfirmation');

Route::get('/forgot-password', ForgotPassword::class)
    ->name('forgotpassword');

    Route::get('/forgot-password-otp', ForgotPasswordOtp::class)
    ->name('forgotpasswordotp');

    Route::get('/change-password', ChangePassword::class)
    ->name('changepassword');

Route::get('/admin-login', AdminLogin::class)
    ->name('admin-login');

Route::get('/page/{slug}', Page::class)
    ->name('page');

Route::post('/save-fcm-token', 'App\Http\Controllers\V1\User\Auth\UserAuthController@storeFcmToken');

