<?php

use App\Http\Controllers\ActionPlanController;
use App\Http\Controllers\CommunityPriceReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\PublicPriceController;
use App\Http\Controllers\RadarController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplyChainController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPriceController::class, 'index'])->name('home');
Route::post('/laporan-harga', [CommunityPriceReportController::class, 'store'])
    ->middleware('throttle:5,60')
    ->name('community-reports.store');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Menu 2: Input Harga dan Tabulasi
Route::prefix('prices')->name('prices.')->group(function () {
    Route::get('/', [PriceController::class, 'index'])->name('index');
    Route::post('/', [PriceController::class, 'store'])->name('store');
    Route::get('/import/template', [PriceController::class, 'downloadImportTemplate'])->name('import.template');
    Route::post('/import', [PriceController::class, 'import'])->name('import');
    Route::get('/evidence/{report}', [PriceController::class, 'evidence'])->name('evidence');
    Route::put('/{id}', [PriceController::class, 'update'])->name('update');
    Route::delete('/{id}', [PriceController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/verify', [PriceController::class, 'verify'])->name('verify');
    Route::post('/sp2kp', [PriceController::class, 'previewSp2kp'])->middleware('throttle:sp2kp')->name('sp2kp.preview');
    Route::post('/sp2kp/import', [PriceController::class, 'importSp2kp'])->middleware('throttle:sp2kp')->name('sp2kp.import');
    Route::post('/corrections/{communityPriceReport}', [CommunityPriceReportController::class, 'review'])->name('corrections.review');
    Route::post('/corrections/{communityPriceReport}/revise', [CommunityPriceReportController::class, 'createOfficialRevision'])->name('corrections.revise');
});

// Menu 3: Master Data
Route::prefix('master')->name('master.')->group(function () {
    Route::get('/', [MasterDataController::class, 'index'])->name('index');

    // Markets
    Route::post('/markets', [MasterDataController::class, 'storeMarket'])->name('markets.store');
    Route::put('/markets/{id}', [MasterDataController::class, 'updateMarket'])->name('markets.update');
    Route::delete('/markets/{id}', [MasterDataController::class, 'destroyMarket'])->name('markets.destroy');

    // Monitoring Points
    Route::post('/points', [MasterDataController::class, 'storePoint'])->name('points.store');
    Route::put('/points/{id}', [MasterDataController::class, 'updatePoint'])->name('points.update');
    Route::delete('/points/{id}', [MasterDataController::class, 'destroyPoint'])->name('points.destroy');

    // Businesses (Mitra)
    Route::post('/businesses', [MasterDataController::class, 'storeBusiness'])->name('businesses.store');
    Route::put('/businesses/{id}', [MasterDataController::class, 'updateBusiness'])->name('businesses.update');
    Route::delete('/businesses/{id}', [MasterDataController::class, 'destroyBusiness'])->name('businesses.destroy');

    // Commodities
    Route::post('/commodities', [MasterDataController::class, 'storeCommodity'])->name('commodities.store');
    Route::put('/commodities/{id}', [MasterDataController::class, 'updateCommodity'])->name('commodities.update');
    Route::delete('/commodities/{id}', [MasterDataController::class, 'destroyCommodity'])->name('commodities.destroy');
});

Route::get('/radar', [RadarController::class, 'index'])->name('radar.index');

Route::prefix('supply')->name('supply.')->group(function () {
    Route::get('/', [SupplyChainController::class, 'index'])->name('index');
    Route::post('/links', [SupplyChainController::class, 'storeLink'])->name('links.store');
    Route::put('/links/{id}', [SupplyChainController::class, 'updateLink'])->name('links.update');
    Route::delete('/links/{id}', [SupplyChainController::class, 'destroyLink'])->name('links.destroy');
    Route::post('/shipments', [SupplyChainController::class, 'storeShipment'])->name('shipments.store');
    Route::put('/shipments/{id}', [SupplyChainController::class, 'updateShipment'])->name('shipments.update');
    Route::delete('/shipments/{id}', [SupplyChainController::class, 'destroyShipment'])->name('shipments.destroy');
});

// Menu 4: Paket Kebijakan
Route::prefix('policies')->name('policies.')->group(function () {
    Route::get('/', [PolicyController::class, 'index'])->name('index');
    Route::post('/rules', [PolicyController::class, 'storeRule'])->name('rules.store');
    Route::post('/simulate', [PolicyController::class, 'simulate'])->name('simulate');
    Route::post('/{id}/toggle', [PolicyController::class, 'toggle'])->name('toggle');
    Route::post('/cases/{id}/decide', [PolicyController::class, 'decide'])->name('cases.decide');
});

// Menu 5: Tindak Lanjut
Route::prefix('actions')->name('actions.')->group(function () {
    Route::get('/', [ActionPlanController::class, 'index'])->name('index');
    Route::post('/', [ActionPlanController::class, 'store'])->name('store');
    Route::get('/evidence/{action}', [ActionPlanController::class, 'evidence'])->name('evidence');
    Route::put('/{id}', [ActionPlanController::class, 'update'])->name('update');
    Route::post('/{id}/status', [ActionPlanController::class, 'updateStatus'])->name('status');
    Route::delete('/{id}', [ActionPlanController::class, 'destroy'])->name('destroy');
});

// Menu 6: Laporan
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
});

// Menu 7: Pengaturan & Role Switcher
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingController::class, 'index'])->name('index');
    Route::post('/users', [SettingController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [SettingController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [SettingController::class, 'destroyUser'])->name('users.destroy');
    Route::post('/notifications', [SettingController::class, 'updateNotificationPrefs'])->name('notifications.update');
});

// Role Switcher for instant testing
Route::get('/switch-user/{id}', [SettingController::class, 'switchUser'])->name('switch.user');
