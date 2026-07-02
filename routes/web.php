<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\CashSummaryController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PointOfSalesController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReturnStockController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\StockOutController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\HomeController;
use App\Services\ModuleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['login' => true]);

// CSRF
Route::get('/refresh-csrf', [\App\Http\Controllers\Api\APIController::class, 'refreshCsrfToken'])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        Route::get('/', function (ModuleService $moduleService) {
            return view('admin.setting.home', [
                'access' => $moduleService->getAccessByModule('System Setting', auth()->user()->group_id),
            ]);
        })->name('home');

        Route::resource('company', CompanyController::class);
        Route::resource('shift', ShiftController::class);
        Route::resource('unit', UnitController::class);
        Route::resource('account', AccountController::class);
        Route::resource('role', GroupController::class);
        Route::resource('system_setting', SystemSettingController::class);

        Route::prefix('profile')->group(function () {
            Route::get('/', [HomeController::class, 'profile'])->name('profile');
            Route::put('/{user:id}/update', [HomeController::class, 'updateProfile'])->name('profile.update');
            Route::put('/{user:id}/password', [HomeController::class, 'updatePassword'])->name('profile.password');
            Route::put('/{user:id}/group', [HomeController::class, 'updateGroup'])->name('profile.group');
            Route::put('/{user:id}/company', [HomeController::class, 'updateCompany'])->name('profile.company');
        });
    });

    Route::group(['prefix' => 'inventory', 'as' => 'inventory.'], function () {
        Route::get('/', function (ModuleService $moduleService) {
            return view('admin.inventory.home', [
                'stats' => $moduleService->getInventoryStats(),
                'access' => $moduleService->getAccessByModule('Inventory', auth()->user()->group_id),
            ]);
        })->name('home');

        Route::resource('category', CategoryController::class);

        Route::resource('product', ProductController::class);
        Route::prefix('product')->name('product.')->group(function () {
            Route::post('product/{product}/variants', [ProductController::class, 'storeVariant'])->name('product-variant.store');
            Route::put('product/{product}/variants/{variant}', [ProductController::class, 'updateVariant'])->name('product-variant.update');
            Route::delete('product/{product}/variants/{variant}', [ProductController::class, 'destroyVariant'])->name('product-variant.destroy');
        });

        Route::resource('stock', StockController::class);
        Route::resource('stock-in', StockInController::class);
        Route::resource('stock-out', StockOutController::class);
        Route::resource('return-stock', ReturnStockController::class);
        Route::resource('stock-opname', StockOpnameController::class)->except(['create', 'edit', 'destroy']);
        Route::prefix('stock-opname')->name('stock-opname.')->group(function () {
            Route::put('/detail/{detail}', [StockOpnameController::class, 'updateDetail'])->name('update-detail');
            Route::put('/{period}/close', [StockOpnameController::class, 'close'])->name('close');
        });

        Route::prefix('report')->as('report.')->group(function () {
            Route::get('/', [\App\Http\Controllers\HomeController::class, 'indexReport'])->name('index');
            Route::get('/generate', [\App\Http\Controllers\HomeController::class, 'generateForm'])->name('generate-form');
            Route::post('/generate', [\App\Http\Controllers\HomeController::class, 'generate'])->name('generate');
            Route::get('/{id}/preview', [\App\Http\Controllers\HomeController::class, 'preview'])->name('preview');
            Route::post('/{id}/print', [\App\Http\Controllers\HomeController::class, 'printReport'])->name('print');
            Route::post('/print-aggregated', [\App\Http\Controllers\HomeController::class, 'printAggregated'])->name('print-aggregated');
            Route::delete('/{id}', [\App\Http\Controllers\HomeController::class, 'destroyReport'])->name('destroy');
        });

        Route::get('stock-log', [StockController::class, 'stockLogs'])->name('stock.logs');
    });

    Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
        Route::get('/', function (ModuleService $moduleService) {
            return view('admin.pos.home', [
                'access' => $moduleService->getAccessByModule('Point Of Sales', auth()->user()->group_id),
            ]);
        })->name('home');

        Route::get('open', [PointOfSalesController::class, 'openCashierView'])->name('open');
        Route::post('open', [PointOfSalesController::class, 'storeCashierOpen'])->name('open.store');
        Route::get('point-of-sales', [PointOfSalesController::class, 'posView'])->name('point-of-sales');

        Route::resource('menu', MenuController::class)->except(['create', 'edit']);
        Route::resource('cash_summary', CashSummaryController::class)->except(['create', 'edit']);
        Route::get('cash_summary/{date}/detail', [CashSummaryController::class, 'detail'])->name('cash_summary.detail');
        Route::delete('cash_summary/destroy-all', [CashSummaryController::class, 'destroyAll'])->name('cash_summary.destroyAll');
        Route::get('report/daily', [SalesReportController::class, 'dailyIndex'])->name('report.daily');
        Route::get('report/weekly', [SalesReportController::class, 'weeklyIndex'])->name('report.weekly');
        Route::get('report/weekly/detail/{start_date}/{end_date}/{company_id}', [SalesReportController::class, 'showWeekly'])->name('report.weekly.detail');
        Route::get('report/monthly', [SalesReportController::class, 'monthlyIndex'])->name('report.monthly');
        Route::get('report/monthly/detail/{start_date}/{end_date}/{company_id}', [SalesReportController::class, 'showMonthly'])->name('report.monthly.detail');
        Route::resource('report', SalesReportController::class)->except('index');
    });

    Route::group(['prefix' => 'hr', 'as' => 'hr.'], function () {
        Route::get('/', function (ModuleService $moduleService) {
            return view('admin.hr.home', [
                'access' => $moduleService->getAccessByModule('Human Resources', auth()->user()->group_id),
            ]);
        })->name('home');
    });

    Route::group(['prefix' => 'presence', 'as' => 'presence.'], function () {
        Route::get('/', function (ModuleService $moduleService) {
            return view('admin.presence.home', [
                'access' => $moduleService->getAccessByModule('Presence', auth()->user()->group_id),
            ]);
        })->name('home');
    });

    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::get('/', [HomeController::class, 'getDashboard'])->name('home');
    });
});
