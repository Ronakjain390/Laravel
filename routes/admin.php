<?php

use App\Http\Livewire\Home\Home;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/dashboard')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('admin/dashboard');
});

Route::prefix('admin')->group(function () {
// allusers
Route::get('/all-users', function () {
    return view('admin.dashboard.allusers.index');
})->name('all-users');

Route::get('/test-users', function () {
    return view('admin.dashboard.allusers.index');
})->name('admin.test-users');
// allusers


// allSubUsers
Route::get('/all-subusers', function () {
    return view('admin.dashboard.allsubusers.index');
})->name('all-subusers');
// allSubUsers

// packages
Route::get('/packages', function () {
    return view('admin.dashboard.packages.index');
})->name('packages');
// packages

// topup
Route::get('/topups', function () {
    return view('admin.dashboard.topups.index');
})->name('topups');
// topup

// topup
Route::get('/pages', function () {
    return view('admin.dashboard.pages.index');
})->name('pages');
// topup

// topup
Route::get('/coupons', function () {
    return view('admin.dashboard.coupons.index');
})->name('coupons');
// topup
});


