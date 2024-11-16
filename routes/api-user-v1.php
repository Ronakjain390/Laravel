<?php

use App\Models\Challan;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\ReturnChallan;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Controllers\V1\Panel\PanelController;
use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\Teams\TeamsController;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\Orders\OrdersController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\Feature\FeatureController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Profile\ProfileController;
use App\Http\Controllers\V1\Section\SectionController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\Templates\TemplatesController;
use App\Http\Controllers\V1\CompanyLogo\CompanyLogoController;
use App\Http\Controllers\V1\FeatureType\FeatureTypeController;
use App\Http\Controllers\V1\PanelDesign\PanelDesignController;
use App\Http\Controllers\V1\PlanFeature\PlanFeatureController;
use App\Http\Controllers\V1\FeatureTopup\FeatureTopupController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\AdditionalFeatures\AdditionalFeaturesController;
use App\Http\Controllers\V1\PlanAdditionalFeature\PlanAdditionalFeatureController;
use App\Http\Controllers\V1\AdditionalFeatureTopup\AdditionalFeatureTopupController;

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

// ------------------------------------------------USER API

Route::post('complete-registration', 'User\Auth\UserAuthController@completeRegistration');
Route::get('user-details', 'User\Auth\UserAuthController@user_details');
Route::get('user-logout', 'User\Auth\UserAuthController@user_logout');

// ------------------------------------------------USER API


// ------------------------------------------------SECTION API
Route::get('section', [SectionController::class, 'index'])->name('section.index');
Route::get('section/{id}', [SectionController::class, 'show'])->name('section.show');

// ------------------------------------------------SECTION API


// ------------------------------------------------PANEL API
Route::get('panel', [PanelController::class, 'index'])->name('panel.index');
Route::get('panel/{id}', [PanelController::class, 'show'])->name('panel.show');

// ------------------------------------------------PANEL API

// ------------------------------------------------FEATURE TYPE API
Route::get('feature-type', [FeatureTypeController::class, 'index'])->name('feature-type.index');
Route::get('feature-type/{id}', [FeatureTypeController::class, 'show'])->name('feature-type.show');

// ------------------------------------------------FEATURE TYPE API

// ------------------------------------------------FEATURE API
Route::get('feature', [FeatureController::class, 'index'])->name('feature.index');
Route::get('feature/{id}', [FeatureController::class, 'show'])->name('feature.show');

// ------------------------------------------------FEATURE API

// ------------------------------------------------ADDITIONAL FEATURES API
Route::get('additional-features', [AdditionalFeaturesController::class, 'index'])->name('additional-features.index');
Route::get('additional-features/{id}', [AdditionalFeaturesController::class, 'show'])->name('additional-features.show');

// ------------------------------------------------ADDITIONAL FEATURES API

// ------------------------------------------------TEMPLATE API
Route::get('template', [TemplatesController::class, 'index'])->name('template.index');
Route::get('template/{id}', [TemplatesController::class, 'show'])->name('template.show');

// ------------------------------------------------TEMPLATE API

// ------------------------------------------------PANELDESIGN API
Route::get('panel-design', [PanelDesignController::class, 'index'])->name('panel-design.index');
Route::get('panel-design/{id}', [PanelDesignController::class, 'show'])->name('panel-design.show');

// ------------------------------------------------PANELDESIGN API

// ------------------------------------------------PANELCOLUMN API

Route::group(['prefix' => 'panel-columns'], function () {
    Route::get('/', [PanelColumnsController::class, 'index'])->name('panel-columns.index');
    Route::get('/{id}', [PanelColumnsController::class, 'show'])->name('panel-columns.show');
    Route::post('/', [PanelColumnsController::class, 'store'])->name('panel-columns.store');
    Route::put('/{id}', [PanelColumnsController::class, 'update'])->name('panel-columns.update');
    Route::patch('/{id}', [PanelColumnsController::class, 'delete'])->name('panel-columns.delete');
    Route::delete('/{id}/destroy', [PanelColumnsController::class, 'destroy'])->name('panel-columns.destroy');
});
// ------------------------------------------------PANELCOLUMN API

// ------------------------------------------------PANELSERIESNUMBER API

Route::group(['prefix' => 'panel-series-numbers'], function () {
    Route::get('/', [PanelSeriesNumberController::class, 'index'])->name('panel-series-numbers.index');
    Route::get('/{id}', [PanelSeriesNumberController::class, 'show'])->name('panel-series-numbers.show');
    Route::post('', [PanelSeriesNumberController::class, 'store'])->name('panel-series-numbers.store');
    Route::put('/{id}', [PanelSeriesNumberController::class, 'update'])->name('panel-series-numbers.update');
    Route::delete('/{id}', [PanelSeriesNumberController::class, 'delete'])->name('panel-series-numbers.delete');
    Route::delete('/{id}/destroy', [PanelSeriesNumberController::class, 'destroy'])->name('panel-series-numbers.destroy');
});
// ------------------------------------------------PANELSERIESNUMBER API

// ------------------------------------------------COMPANYLOGO API

Route::group(['prefix' => 'company-logo'], function () {
    Route::get('/', [CompanyLogoController::class, 'index'])->name('company-logo.index');
    Route::get('/{id}', [CompanyLogoController::class, 'show'])->name('company-logo.show');
    Route::post('', [CompanyLogoController::class, 'store'])->name('company-logo.store');
    Route::put('/{id}', [CompanyLogoController::class, 'update'])->name('company-logo.update');
    Route::delete('/{id}', [CompanyLogoController::class, 'delete'])->name('company-logo.delete');
    Route::delete('/{id}/destroy', [CompanyLogoController::class, 'destroy'])->name('company-logo.destroy');
});
// ------------------------------------------------COMPANYLOGOCOMPANYLOGO API

// ------------------------------------------------PLANS API

Route::get('/plans', [PlansController::class, 'index'])->name('plans.index');
Route::get('/plans/{id}', [PlansController::class, 'show'])->name('plans.show');

// ------------------------------------------------PLANS API

// ------------------------------------------------FEATURE TOPUPS API

Route::get('/feature-topups', [FeatureTopupController::class, 'index'])->name('feature_topups.index');
Route::get('/feature-topups/{id}', [FeatureTopupController::class, 'show'])->name('feature_topups.show');

// ------------------------------------------------FEATURE TOPUPS API

// ------------------------------------------------ADDITIONAL ADDITIONAL FEATURE TOPUPS API

Route::get('/additional-feature-topups', [AdditionalFeatureTopupController::class, 'index'])->name('additional_feature_topups.index');
Route::get('/additional-feature-topups/{id}', [AdditionalFeatureTopupController::class, 'show'])->name('additional_feature_topups.show');

// ------------------------------------------------ADDITIONAL ADDITIONAL FEATURE TOPUPS API

// ------------------------------------------------ORDER API

Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
Route::post('/orders/topup', [OrdersController::class, 'topupOrderStore'])->name('orders.topup');
Route::get('/orders/user', [OrdersController::class, 'userIndex'])->name('orders.user');
Route::get('/orders/{id}', [OrdersController::class, 'show'])->name('orders.show');

// ------------------------------------------------ORDER API

// ------------------------------------------------ADD RECEIVER API
// Add a receiver
Route::post('/receivers/add-receiver', [ReceiversController::class, 'addReceiver'])->name('receivers.addReceiver');

// Add a manual receiver
Route::post('/receivers/add-manual-receiver', [ReceiversController::class, 'addManualReceiver'])->name('receivers.addManualReceiver');

// Store receiver details
Route::post('/receivers/store-receiver-detail', [ReceiversController::class, 'storeReceiverDetail'])->name('receivers.storeReceiverDetail');

// Update a receiver
Route::put('/receivers/update-receiver/{receiverId}', [ReceiversController::class, 'updateReceiver'])->name('receivers.updateReceiver');

// Update a receiver detail
Route::put('/receivers/update-receiver-detail/{receiverDetailId}', [ReceiversController::class, 'updateReceiverDetail'])->name('receivers.updateReceiverDetail');

// Get all receivers with filters
Route::get('/receivers', [ReceiversController::class, 'index'])->name('receivers.index');

// Get a specific user by special ID
Route::get('/receivers/show-user/{special_id}', [ReceiversController::class, 'showUser'])->name('receivers.showUser');

// Get a specific receiver by ID
Route::get('/receivers/show/{id}', [ReceiversController::class, 'show'])->name('receivers.show');

Route::delete('/receivers/{id}', [ReceiversController::class, 'delete'])->name('receivers.delete');

// Fetch State and City
Route::get('/receivers/fetch-city-state/{pincode}', [ReceiversController::class, 'fetchCityAndStateByPincode'])
    ->name('receivers/fetch.city.state.by.pincode');

// ------------------------------------------------ADD RECEIVER API


// ------------------------------------------------ADD BUYER API
// Add a Buyer
Route::post('/buyers/add-buyer', [BuyersController::class, 'addBuyer'])->name('buyers.addBuyer');

// Add a manual Buyer
Route::post('/buyers/add-manual-buyer', [BuyersController::class, 'addManualBuyer'])->name('buyers.addManualBuyer');

// Store Buyer details
Route::post('/buyers/store-buyer-detail', [BuyersController::class, 'storeBuyerDetail'])->name('buyers.storeBuyerDetail');

// Update a Buyer
Route::put('/buyers/update-buyer/{buyerId}', [BuyersController::class, 'updateBuyer'])->name('buyers.updateBuyer');

// Update a Buyer detail
Route::put('/buyers/update-buyer-detail/{buyerDetailId}', [BuyersController::class, 'updateBuyerDetail'])->name('buyers.updateBuyerDetail');

// Get all Buyers with filters
Route::get('/buyers', [BuyersController::class, 'index'])->name('buyers.index');

// Get a specific user by special ID
Route::get('/buyers/show-user/{special_id}', [BuyersController::class, 'showUser'])->name('buyers.showUser');

// Get a specific Buyer by ID
Route::get('/buyers/show/{id}', [BuyersController::class, 'show'])->name('buyers.show');

Route::delete('/buyers/{id}', [BuyersController::class, 'delete'])->name('buyers.delete');

// Fetch State and City
Route::get('/buyers/fetch-city-state/{pincode}', [BuyersController::class, 'fetchCityAndStateByPincode'])
    ->name('buyers/fetch.city.state.by.pincode');

// ------------------------------------------------ADD BUYER API

// ------------------------------------------------ADD UNITS API

// List all units
Route::get('units', [UnitsController::class, 'index']);

// Create a new unit
Route::post('units', [UnitsController::class, 'store']);

// Show a specific unit
Route::get('units/{unit}', [UnitsController::class, 'show']);

// Update a specific unit
Route::post('units/{unit}', [UnitsController::class, 'update']);

// Delete a specific unit
Route::delete('units/{unit}', [UnitsController::class, 'destroy']);

// ------------------------------------------------ADD UNITS API


// ------------------------------------------------CHALLAN API
// Route group for Challan routes
Route::prefix('challan')->group(function () {
    // Store a new Challan
    Route::post('/', [ChallanController::class, 'store']);

    // Get a list of Challans
    Route::get('/', [ChallanController::class, 'index']);
    // Export csv sheet
    Route::get('/export-columns', [ChallanController::class, 'exportColumns'])->name('export.columns');

    // Get a list of Check Balance
    Route::get('/index-check-balance', [ChallanController::class, 'indexCheckBalance']);

    // Route for specific Challan actions
    Route::prefix('{challanId}')->group(function () {
        // Get details of a specific Challan
        Route::get('/', [ChallanController::class, 'show']);

        // Update an existing Challan
        Route::post('/', [ChallanController::class, 'update']);

        // Send a Challan for acceptance
        Route::post('/send', [ChallanController::class, 'send']);

        // Resend a Challan
        Route::post('/resend', [ChallanController::class, 'resend']);

        // Modify a Challan
        Route::post('/modify', [ChallanController::class, 'modify']);

        // Accept a Challan
        Route::post('/accept', [ChallanController::class, 'accept']);

        // Self-Accept a Challan
        Route::post('/self-accept', [ChallanController::class, 'selfAccept']);

        // Reject a Challan
        Route::post('/reject', [ChallanController::class, 'reject']);

        // Self-Reject a Challan
        Route::post('/self-reject', [ChallanController::class, 'selfReject']);

        // Delete a Challan
        Route::delete('/', [ChallanController::class, 'delete']);

        // Force Delete a Challan
        Route::delete('/force-delete', [ChallanController::class, 'forceDelete']);
    });
});

// ------------------------------------------------CHALLAN API

// ------------------------------------------------RETURN CHALLAN API

// Route group for ReturnChallan routes
Route::prefix('return-challans')->group(function () {
    // Store a new ReturnChallan
    Route::post('/', [ReturnChallanController::class, 'store']);

    // Get a list of ReturnChallans
    Route::get('/', [ReturnChallanController::class, 'index']);
    // Sender List of a Return Challan
    Route::get('/get-sender', [ReturnChallanController::class, 'getSender'])->name('getSender');

    // Sender Details of a Return Challan
    Route::get('/get-sender-data', [ReturnChallanController::class, 'getSenderData'])->name('getSenderData');

    // All Sender Data of a Return Challan
    Route::get('/get-all-sender-data', [ReturnChallanController::class, 'getallSenderData'])->name('getallSenderData');
    // Route for specific ReturnChallan actions
    Route::prefix('{returnChallanId}')->group(function () {
        // Get details of a specific ReturnChallan
        Route::get('/', [ReturnChallanController::class, 'show']);

        // Update an existing ReturnChallan
        Route::post('/', [ReturnChallanController::class, 'update']);

        // Send a ReturnChallan for acceptance
        Route::post('/send', [ReturnChallanController::class, 'send']);

        // Resend a ReturnChallan
        Route::post('/resend', [ReturnChallanController::class, 'resend']);

        // Modify a ReturnChallan
        Route::post('/modify', [ReturnChallanController::class, 'modify']);

        // Accept a ReturnChallan
        Route::post('/accept', [ReturnChallanController::class, 'accept']);

        // Self-Accept a ReturnChallan
        Route::post('/self-accept', [ReturnChallanController::class, 'selfAccept']);

        // Reject a ReturnChallan
        Route::post('/reject', [ReturnChallanController::class, 'reject']);

        // Self-Reject a ReturnChallan
        Route::post('/self-reject', [ReturnChallanController::class, 'selfReject']);

        // Delete a ReturnChallan
        Route::delete('/', [ReturnChallanController::class, 'delete']);

        // Force Delete a ReturnChallan
        Route::delete('/force-delete', [ReturnChallanController::class, 'forceDelete']);
    });
});
// ------------------------------------------------RETURN CHALLAN API

// ------------------------------------------------BULK PRODUCTS API

Route::post('products/bulk-store', [ProductController::class, 'bulkStore'])->name('products.bulkStore');
Route::get('products/export-columns', [ProductController::class, 'exportColumns'])->name('products.exportColumns');

// ------------------------------------------------BULK PRODUCTS API
// ------------------------------------------------INVOICE API
// Route group for Invoice routes
Route::prefix('invoice')->group(function () {
    // Store a new Invoice
    Route::post('/', [InvoiceController::class, 'store']);

    // Get a list of Invoices
    Route::get('/', [InvoiceController::class, 'index']);

    // Route for specific Invoice actions
    Route::prefix('{invoiceId}')->group(function () {
        // Get details of a specific Invoice
        Route::get('/', [InvoiceController::class, 'show']);

        // Update an existing Invoice
        Route::post('/', [InvoiceController::class, 'update']);

        // Send a Invoice for acceptance
        Route::post('/send', [InvoiceController::class, 'send']);

        // Resend a Invoice
        Route::post('/resend', [InvoiceController::class, 'resend']);

        // Modify a Invoice
        Route::post('/modify', [InvoiceController::class, 'modify']);

        // Accept a Invoice
        Route::post('/accept', [InvoiceController::class, 'accept']);

        // Self-Accept a Invoice
        Route::post('/self-accept', [InvoiceController::class, 'selfAccept']);

        // Reject a Invoice
        Route::post('/reject', [InvoiceController::class, 'reject']);

        // Self-Reject a Invoice
        Route::post('/self-reject', [InvoiceController::class, 'selfReject']);

        // Delete a Invoice
        Route::delete('/', [InvoiceController::class, 'delete']);

        // Force Delete a Invoice
        Route::delete('/force-delete', [InvoiceController::class, 'forceDelete']);
    });
});

// ------------------------------------------------INVOICE API


// ------------------------------------------------TEST API

Route::get('/get-user-active-plan', [OrdersController::class, 'userActivePlan']);
// ------------------------------------------------TEST API

// ------------------------------------------------PROFILE API

Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
Route::post('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');

// ------------------------------------------------PROFILE API

//-------------------------------------------------TeamController
Route::get('/teams', [TeamsController::class, 'index']);
Route::get('/teams/{id}', [TeamsController::class, 'show']);
Route::post('/teams', [TeamsController::class, 'store']);
Route::post('/teams/{id}', [TeamsController::class, 'update']);
Route::delete('/teams/{id}', [TeamsController::class, 'destroy']); // New delete route

//-------------------------------------------------Routes for TeamUserController
Route::get('/team-users', [TeamUserController::class, 'index']);
Route::get('/team-users/{id}', [TeamUserController::class, 'show']);
Route::post('/team-users', [TeamUserController::class, 'store']);
Route::post('/team-users/{id}', [TeamUserController::class, 'update']);
Route::delete('/team-users/{id}', [TeamUserController::class, 'destroy']);


Route::group(['prefix' => 'api/v1/product'], function () {
    Route::get('product-search-barcode', [ProductController::class, 'productCode']);
});
