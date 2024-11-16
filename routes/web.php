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
// use App\Http\Controllers\V1\Payment\PaymentController;
use App\Http\Livewire\AcceptRejectChallanWithOtp\AcceptRejectChallanWithOtp;
use App\Http\Livewire\AcceptRejectDocumentWithOtp\AcceptRejectDocumentWithOtp;


// Admin Routes
// Route::prefix('admin-dashboard')->group(function () {
//     Route::get('/', function () {
//         return view('admin.dashboard.index');
//     })->name('admin-dashboard');
// });

// allusers
// Route::get('/all-users', function () {
//     return view('admin.dashboard.allusers.index');
// })->name('all-users');
// allusers

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

// Route::post('/payment-initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiatePayment');

// Route::get('/team-member/register', Register::class)
//     ->name('team-member.register');

// Route::get('/team-member/otp-login', OtpLogin::class)
//     ->name('team-member.otplogin');

// Route::get('/team-member/otp-confirmation', OtpConfirmation::class)
//     ->name('team-member.otpconfirmation');

// Route::get('/team-member/forgot-password', ForgotPassword::class)
//     ->name('team-member.forgotPassword');

// Route::get('/logout', Logout::class)
//     ->name('team-member.logout');


// Route::get('/plan-user', [ProfileController::class,'planUser']);
Route::get('/transfer-data', [DataTransferController::class,'transferData']);
// Route::get('/transfer-data-in', [DataTransferController::class,'query_in']);
Route::get('/addcolumn', [DataTransferController::class,'addcolumn']);
Route::get('/permissions', [DataTransferController::class,'permissions']);
Route::get('/test-free-plan', [DataTransferController::class, 'freePlan']);
Route::get('/addunit', [DataTransferController::class,'addunit']);
Route::get('/update-permissions', [DataTransferController::class, 'updatePermissions']);
