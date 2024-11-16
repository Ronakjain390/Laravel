<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Controllers\V1\Panel\PanelController;
use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\Orders\OrdersController;
use App\Http\Controllers\V1\Feature\FeatureController;
use App\Http\Controllers\V1\Section\SectionController;
use App\Http\Controllers\V1\Templates\TemplatesController;
use App\Http\Controllers\V1\Admin\Auth\AdminAuthController;
use App\Http\Controllers\V1\FeatureType\FeatureTypeController;
use App\Http\Controllers\V1\PanelDesign\PanelDesignController;
use App\Http\Controllers\V1\PlanFeature\PlanFeatureController;
use App\Http\Controllers\V1\FeatureTopup\FeatureTopupController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PanelFeatures\PanelFeaturesController;
use App\Http\Controllers\V1\AdditionalFeatures\AdditionalFeaturesController;
use App\Http\Controllers\V1\PlanAdditionalFeature\PlanAdditionalFeatureController;
use App\Http\Controllers\V1\AdditionalFeatureTopup\AdditionalFeatureTopupController;
use App\Http\Controllers\V1\Page\PageController;

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

Route::get('admin-details', [AdminAuthController::class, 'admin_details'])->name('admin-details');
Route::get('logout', [AdminAuthController::class, 'admin_logout'])->name('logout');
Route::get('/users', [AdminAuthController::class, 'index'])->name('admin.index');


// ------------------------------------------------ADMIN API

// ------------------------------------------------Terms and Conditions API
Route::prefix('admin')->group(function () {
    Route::get('pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('pages/create', [PageController::class, 'create'])->name('pages.create');
    Route::post('pages', [PageController::class, 'store'])->name('pages.store');
    Route::get('pages/{page}', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::delete('pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
});
// ------------------------------------------------Terms and Conditions API


// ------------------------------------------------SECTION API
Route::post('section', [SectionController::class, 'store'])->name('section.store');
Route::get('section', [SectionController::class, 'index'])->name('section.index');
Route::get('section/{id}', [SectionController::class, 'show'])->name('section.show');
Route::post('section/{id}', [SectionController::class, 'update'])->name('section.update');
Route::patch('section/{id}', [SectionController::class, 'delete'])->name('section.delete');
Route::delete('section/{id}', [SectionController::class, 'destroy'])->name('section.destroy');

// ------------------------------------------------SECTION API


// ------------------------------------------------PANEL API
Route::post('panel', [PanelController::class, 'store'])->name('panel.store');
Route::get('panel', [PanelController::class, 'index'])->name('panel.index');
Route::get('panel/{id}', [PanelController::class, 'show'])->name('panel.show');
Route::post('panel/{id}', [PanelController::class, 'update'])->name('panel.update');
Route::patch('panel/{id}', [PanelController::class, 'delete'])->name('panel.delete');
Route::delete('panel/{id}', [PanelController::class, 'destroy'])->name('panel.destroy');

// ------------------------------------------------PANEL API

// ------------------------------------------------FEATURE TYPE API
Route::post('feature-type', [FeatureTypeController::class, 'store'])->name('feature-type.store');
Route::get('feature-type', [FeatureTypeController::class, 'index'])->name('feature-type.index');
Route::get('feature-type/{id}', [FeatureTypeController::class, 'show'])->name('feature-type.show');
Route::post('feature-type/{id}', [FeatureTypeController::class, 'update'])->name('feature-type.update');
Route::patch('feature-type/{id}', [FeatureTypeController::class, 'delete'])->name('feature-type.delete');
Route::delete('feature-type/{id}', [FeatureTypeController::class, 'destroy'])->name('feature-type.destroy');

// ------------------------------------------------FEATURE TYPE API

// ------------------------------------------------FEATURE API
Route::post('feature', [FeatureController::class, 'store'])->name('feature.store');
Route::get('feature', [FeatureController::class, 'index'])->name('feature.index');
Route::get('feature/{id}', [FeatureController::class, 'show'])->name('feature.show');
Route::post('feature/{id}', [FeatureController::class, 'update'])->name('feature.update');
Route::patch('feature/{id}', [FeatureController::class, 'delete'])->name('feature.delete');
Route::delete('feature/{id}', [FeatureController::class, 'destroy'])->name('feature.destroy');

// ------------------------------------------------FEATURE API

// ------------------------------------------------ADDITIONAL FEATURES API
Route::post('additional-features', [AdditionalFeaturesController::class, 'store'])->name('additional-features.store');
Route::get('additional-features', [AdditionalFeaturesController::class, 'index'])->name('additional-features.index');
Route::get('additional-features/{id}', [AdditionalFeaturesController::class, 'show'])->name('additional-features.show');
Route::post('additional-features/{id}', [AdditionalFeaturesController::class, 'update'])->name('additional-features.update');
Route::patch('additional-features/{id}', [AdditionalFeaturesController::class, 'delete'])->name('additional-features.delete');
Route::delete('additional-features/{id}', [AdditionalFeaturesController::class, 'destroy'])->name('additional-features.destroy');

// ------------------------------------------------ADDITIONAL FEATURES API

// ------------------------------------------------TEMPLATE API
Route::post('template', [TemplatesController::class, 'store'])->name('template.store');
Route::get('template', [TemplatesController::class, 'index'])->name('template.index');
Route::get('template/{id}', [TemplatesController::class, 'show'])->name('template.show');
Route::post('template/{id}', [TemplatesController::class, 'update'])->name('template.update');
Route::patch('template/{id}', [TemplatesController::class, 'delete'])->name('template.delete');
Route::delete('template/{id}', [TemplatesController::class, 'destroy'])->name('template.destroy');

// ------------------------------------------------TEMPLATE API

// ------------------------------------------------PANELDESIGN API
Route::post('panel-design', [PanelDesignController::class, 'store'])->name('panel-design.store');
Route::get('panel-design', [PanelDesignController::class, 'index'])->name('panel-design.index');
Route::get('panel-design/{id}', [PanelDesignController::class, 'show'])->name('panel-design.show');
Route::post('panel-design/{id}', [PanelDesignController::class, 'update'])->name('panel-design.update');
Route::patch('panel-design/{id}', [PanelDesignController::class, 'delete'])->name('panel-design.delete');
Route::delete('panel-design/{id}', [PanelDesignController::class, 'destroy'])->name('panel-design.destroy');

// ------------------------------------------------PANELDESIGN API

// ------------------------------------------------PLANS API

Route::get('/plans', [PlansController::class, 'index'])->name('plans.index');
Route::get('/plans/{id}', [PlansController::class, 'show'])->name('plans.show');
Route::post('/plans', [PlansController::class, 'store'])->name('plans.store');
Route::put('/plans/{id}', [PlansController::class, 'update'])->name('plans.update');
Route::patch('/plans/{id}', [PlansController::class, 'delete'])->name('plans.delete');
Route::delete('/plans/{id}', [PlansController::class, 'destroy'])->name('plans.destroy');

// ------------------------------------------------PLANS API

// ------------------------------------------------PLAN FEATURES API

Route::post('/plan-features', [PlanFeatureController::class, 'store'])->name('plan_features.store');
Route::put('/plan-features/{id}', [PlanFeatureController::class, 'update'])->name('plan_features.update');
Route::patch('/plan-features/{id}', [PlanFeatureController::class, 'delete'])->name('plan_features.delete');
Route::delete('/plan-features/{id}', [PlanFeatureController::class, 'destroy'])->name('plan_features.destroy');

// ------------------------------------------------PLAN FEATURES API

// ------------------------------------------------PLAN ADDITIONAL FEATURES API

Route::post('/plan-additional-features', [PlanAdditionalFeatureController::class, 'store'])->name('plan_additional_features.store');
Route::put('/plan-additional-features/{id}', [PlanAdditionalFeatureController::class, 'update'])->name('plan_additional_features.update');
Route::patch('/plan-additional-features/{id}', [PlanAdditionalFeatureController::class, 'delete'])->name('plan_additional_features.delete');
Route::delete('/plan-additional-features/{id}', [PlanAdditionalFeatureController::class, 'destroy'])->name('plan_additional_features.destroy');

// ------------------------------------------------PLAN ADDITIONAL FEATURES API

// ------------------------------------------------FEATURE TOPUPS API

Route::post('/feature-topups', [FeatureTopupController::class, 'store'])->name('feature_topups.store');
Route::get('/feature-topups', [FeatureTopupController::class, 'index'])->name('feature_topups.index');
Route::get('/feature-topups/{id}', [FeatureTopupController::class, 'show'])->name('feature_topups.show');
Route::put('/feature-topups/{id}', [FeatureTopupController::class, 'update'])->name('feature_topups.update');
Route::patch('/feature-topups/{id}', [FeatureTopupController::class, 'delete'])->name('feature_topups.delete');
Route::delete('/feature-topups/{id}', [FeatureTopupController::class, 'destroy'])->name('feature_topups.destroy');

// ------------------------------------------------FEATURE TOPUPS API

// ------------------------------------------------ADDITIONAL FEATURE TOPUPS API

Route::post('/additional-feature-topups', [AdditionalFeatureTopupController::class, 'store'])->name('additional_feature_topups.store');
Route::get('/additional-feature-topups', [AdditionalFeatureTopupController::class, 'index'])->name('additional_feature_topups.index');
Route::get('/additional-feature-topups/{id}', [AdditionalFeatureTopupController::class, 'show'])->name('additional_feature_topups.show');
Route::put('/additional-feature-topups/{id}', [AdditionalFeatureTopupController::class, 'update'])->name('additional_feature_topups.update');
Route::patch('/additional-feature-topups/{id}', [AdditionalFeatureTopupController::class, 'delete'])->name('additional_feature_topups.delete');
Route::delete('/additional-feature-topups/{id}', [AdditionalFeatureTopupController::class, 'destroy'])->name('additional_feature_topups.destroy');

// ------------------------------------------------ADDITIONAL FEATURE TOPUPS API

// ------------------------------------------------ORDER API

Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
Route::post('/orders/topup', [OrdersController::class, 'topupOrderStore'])->name('orders.topup');
Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
Route::get('/orders/{id}', [OrdersController::class, 'show'])->name('orders.show');

// ------------------------------------------------ORDER API


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

