<?php
namespace App\Http\Livewire;
use App\Http\Livewire\Home\Home;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Home\Auth\Logout;
use App\Services\PDFServices\PDFWhatsAppService;
use App\Http\Livewire\Setting\Screens\Notification;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\GoodsReceipt\GoodsReceiptsController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Payment\PaymentController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\Buyers\BuyersController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\V1\PurchaseOrder\PurchaseOrderController;

// use App\Http\Controllers\V1\Admin\Auth\AdminAuthController;

// route::get('/sender-pdf', function () {
//     return view('pdf.sender.challan_pdf');
// });
// Route::post('/register', [AdminAuthController::class, 'register'])->name('register');
Route::get('/example', function () {
    return view('livewire.example-component');
});
Route::get('/logout', Logout::class)
    ->name('logout');
Route::prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('user.dashboard.index');
    })->name('dashboard');
    Route::get('/goods-receipt-note', [GoodsReceiptsController::class, 'showPdf'])->name('showPdf');

    Route::get('/pdf-proxy', function (Request $request) {
        $pdfUrl = $request->query('url');
        // For debugging: Output the decoded URL
        \Log::info("Decoded URL: " . urldecode($pdfUrl));

        // Fetch the PDF content using GuzzleHttp or similar
        $client = new \GuzzleHttp\Client();
        $response = $client->get($pdfUrl);
        $contentType = $response->getHeaderLine('Content-Type');

        if ($contentType === 'application/pdf') {
            return response($response->getBody())->header('Content-Type', $contentType);
        } else {
            abort(404);
        }
    });
});

// Route::get('/goods-receipt-note/{pdfUrl}', 'GoodsReceiptsController@showPdf');
// stock
Route::get('/stock', function () {
    return view('user.dashboard.stock.index');
})->name('stock');
// stock


// Route::get('/pdf/{path}', function ($path) {
//     $url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5));
//     $headers = get_headers($url, 1);
//     header('Content-Type: ' . $headers['Content-Type']);
//     echo file_get_contents($url);
// })->where('path', '.*');

// pricing
Route::get('/pricing', function () {
    return view('user.dashboard.pricing.index');
})->name('pricing');
// pricing


// how the parchi works
Route::get('/how-theparchi-works', function () {
    return view('user.dashboard.parchiWorks.index');
})->name('how-theparchi-works');
// how the parchi works

// how the parchi works
Route::get('/help', function () {
    return view('user.dashboard.help.index');
})->name('help');
// how the parchi works


// topup
Route::get('/topup', function () {
    return view('user.dashboard.pricing.topup');
})->name('topup');
// topup

// checkout
Route::get('/checkout', function () {
    return view('user.dashboard.pricing.checkout');
})->name('checkout');
// checkout

// All features
Route::get('/all-feature', function () {
    return view('user.dashboard.allfeature.index');
})->name('all-feature');
// All features


Route::post('/wallet/top-up', [WalletController::class, 'topUp']);

Route::prefix('sender')->group(function () {
    // Route::get('/', function () {
    //     return view('user.sender.index');
    // })->name('sender');

    Route::get('/{template?}', function ($template = 'index') {
        return view('user.sender.index');
    })->name('sender');
    // ------------------------------------------------CHALLAN
    // Route::get('/sent-challan', function () {
    //     return view('user.sender.sent-challan');
    // })->name('sent-challan');
    // Route::get('check-balance', \App\Http\Livewire\Sender\Screens\CheckBalance::class)->name('check-balance');


    // ------------------------------------------------BULK CHALLAN

    // Route::get('/challan/export-columns', [ChallanController::class, 'exportColumns'])->name('sender.exportColumns');
    Route::get('/challan/export-columns/{option}', [ChallanController::class, 'exportColumns'])->name('sender.exportColumns');


    // ------------------------------------------------BULK CHALLAN

    // ------------------------------------------------EXPORT CHALLAN

    Route::get('/export-challan', [ChallanController::class, 'exportChallan'])->name('challan.exportChallan');

    // ------------------------------------------------EXPORT CHALLAN

    // ------------------------------------------------EXPORT DETAILED CHALLAN

    Route::get('/export-detailed-challan', [ChallanController::class, 'exportDetailedChallan'])->name('challan.exportDetailedChallan');

    // ------------------------------------------------EXPORT DETAILED CHALLAN

    // ------------------------------------------------EXPORT CHALLAN CHECLK BALANCE

    Route::get('/export/check-balance/challan', [ChallanController::class, 'exportCheckBalanceChallan'])->name('challan.exportCheckBalanceChallan');

    // ------------------------------------------------EXPORT CHALLAN CHECLK BALANCE

    // Route::get('signaturepad', [SignaturePadController::class, 'index']);
    Route::post('signaturepad', [ChallanController::class, 'uploadSignature'])->name('signaturepad.uploadSignature');

});

Route::prefix('receiver')->group(function () {
    // Route::get('/', function () {
    //     return view('user.receiver.index');
    // })->name('receiver');

    Route::get('/{template?}', function ($template = 'index') {
        return view('user.receiver.index');
    })->name('receiver');
      // ------------------------------------------------EXPORT CHALLAN

      Route::get('/export-return-challan', [ReturnChallanController::class, 'exportReturnChallan'])->name('challan.exportReturnChallan');

     // ------------------------------------------------EXPORT DETAILED CHALLAN

     Route::get('/export-detailed-return-challan', [ReturnChallanController::class, 'exportDetailedReturnChallan'])->name('challan.exportDetailedReturnChallan');

     Route::get('/receiver/export-columns', [ReceiversController::class, 'exportColumns'])->name('receiver.exportColumns');

});

// Route::get('accept-challan/{challanId}', [ChallanController::class, 'acceptChallan'])->name('accept.challan');


// Route::get('accept-challan/{challanId}', AcceptRejectChallanWithOtp::class)->name('accept.challan');



// Route::get('accept-challan/{challanId}', [AcceptRejectChallanWithOtp::class, 'render'])->name('accept.challan');


// ------------------------------------------------BULK PRODUCTS

// Route::post('products/bulk-store', [ProductController::class, 'bulkStore'])->name('products.bulkStore');
Route::get('products/export-columns', [ProductController::class, 'exportColumns'])->name('products.exportColumns');
Route::get('/export-products', [ProductController::class, 'exportProducts'])->name('products.exportProducts');

// ------------------------------------------------BULK PRODUCTS


// ------------------------------------------------SELLER

Route::prefix('seller')->group(function () {
    Route::get('/{template?}', function ($template = 'index') {
        return view('user.seller.index');
    })->name('seller');

    Route::get('/invoice/export-columns/{option}', [InvoiceController::class, 'exportColumns'])->name('seller.exportColumns');
    // Route::get('/challan/export-columns/{option}', [ChallanController::class, 'exportColumns'])->name('sender.exportColumns');

    // ------------------------------------------------EXPORT CHALLAN

    Route::get('/export-invoice', [InvoiceController::class, 'exportInvoice'])->name('challan.exportInvoice');

    // ------------------------------------------------EXPORT DETAILED CHALLAN

    Route::get('/export-detailed-invoice', [InvoiceController::class, 'exportDetailedInvoice'])->name('challan.exportDetailedInvoice');
});

// ------------------------------------------------SELLER


// ------------------------------------------------BUYER
Route::prefix('buyer')->group(function () {
    Route::get('/{template?}', function ($template = 'index') {
        return view('user.buyer.index');
    })->name('buyer');
          // ------------------------------------------------EXPORT PURCHASE ORDER

          Route::get('/export-purchase-order', [PurchaseOrderController::class, 'exportPurchaseOrder'])->name('buyer.exportPurchaseOrder');

          // ------------------------------------------------EXPORT DETAILED PURCHASE ORDER

          Route::get('/export-detailed-purchase-order', [PurchaseOrderController::class, 'exportDetailedPurchaseOrder'])->name('buyer.exportDetailedPurchaseOrder');
          Route::get('/buyer/export-columns', [BuyersController::class, 'exportColumns'])->name('buyer.exportColumns');
});

// ------------------------------------------------BUYER

// ------------------------------------------------Goods Receipt Note
Route::prefix('goods-receipt-note')->group(function () {
    Route::get('/{template?}', function ($template = 'index') {
        // dd($template);
        return view('user.grn.index');
    })->name('grn');
});

// setting
Route::prefix('setting')->group(function () {
    Route::get('/', function () {
        return view('user.setting.index');
    })->name('setting');

    Route::get('/teams', function () {
        return view('user.setting.teams');
    })->name('teams');

    Route::get('/team-member', function () {
        return view('user.setting.teamMember');
    })->name('team-member');

    Route::get('/active-plans', function () {
        return view('user.setting.activePlans');
    })->name('active-plans');

    Route::get('order-history', function () {
        return view('user.setting.orderHistory');
    })->name('order-history');

    Route::get('company-logo', function () {
        return view('user.setting.companyLogo');
    })->name('company-logo');

    // profile
    Route::get('/profile', function () {
        return view('user.setting.profile');
    })->name('profile');
    // profile

    Route::get('user-address', function () {
        return view('user.setting.userAddress');
    })->name('user-address');

    Route::get('notification', function () {
        return view('user.setting.notification');
    })->name('notification');

    Route::get('whatsapp-logs', function () {
        return view('user.setting.whatsappLogs');
    })->name('whatsapp-logs');

    Route::get('tabs-component', function () {
        return view('user.setting.tabsComponent');
    })->name('tabs-component');
});
// setting

Route::get('/send-whatsapp', 'App\Services\PDFServices\PDFWhatsAppService@sendChallanOnWhatsApp');
// Payment
// Route::post('/payment-initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiatePayment');

Route::post('/payment-initiate', 'App\Http\Livewire\Dashboard\Checkout@initiatePayment');

Route::post('/payment-initiate-wallet', [Notification::class, 'initiatePayment']);

Route::post('/delete-account', 'UserAuthController@deleteAccount')->name('delete.account');

Route::post('/save-fcm-token', 'App\Http\Controllers\V1\User\Auth\UserAuthController@storeFcmToken')->name('save-fcm-token');
// Payment

