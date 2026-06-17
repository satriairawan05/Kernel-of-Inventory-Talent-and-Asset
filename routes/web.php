<?php

use Illuminate\Support\Facades\{Auth, Route};


Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['login' => true, 'register' => true]);

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        Route::get('/', function () {
            return view('admin.setting.home');
        })->name('home');

        Route::resource('company', App\Http\Controllers\Admin\CompanyController::class);
        Route::resource('shift', \App\Http\Controllers\Admin\ShiftController::class);
        Route::resource('unit', \App\Http\Controllers\Admin\UnitController::class);
        Route::resource('account', App\Http\Controllers\Admin\AccountController::class);
        Route::resource('role',\App\Http\Controllers\Admin\GroupController::class);

        Route::middleware(['auth'])->prefix('profile')->group(function () {
            Route::get('/', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');
            Route::put('/{user:id}/update', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('profile.update');
            Route::put('/{user:id}/password', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('profile.password');
            Route::put('/{user:id}/group', [App\Http\Controllers\HomeController::class, 'updateGroup'])->name('profile.group');
            Route::put('/{user:id}/company', [App\Http\Controllers\HomeController::class, 'updateCompany'])->name('profile.company');
        });
    });

    Route::group(['prefix' => 'inventory', 'as' => 'inventory.'], function () {
        Route::get('/', function (\App\Services\ModuleService $moduleService) {
            return view('admin.inventory.home', [
                'stats' => $moduleService->getInventoryStats()
            ]);
        })->name('home');

        Route::resource('category', \App\Http\Controllers\Admin\CategoryController::class);

        Route::resource('product', \App\Http\Controllers\Admin\ProductController::class);
        Route::prefix('product')->name('product.')->group(function () {
            Route::post('product/{product}/variants', [\App\Http\Controllers\Admin\ProductController::class, 'storeVariant'])->name('product-variant.store');
            Route::put('product/{product}/variants/{variant}', [\App\Http\Controllers\Admin\ProductController::class, 'updateVariant'])->name('product-variant.update');
            Route::delete('product/{product}/variants/{variant}', [\App\Http\Controllers\Admin\ProductController::class, 'destroyVariant'])->name('product-variant.destroy');
        });

        Route::resource('stock', \App\Http\Controllers\Admin\StockController::class);
        Route::resource('stock-in', \App\Http\Controllers\Admin\StockInController::class);
        Route::get('stock-log', [\App\Http\Controllers\Admin\StockController::class, 'stockLogs'])->name('stock.logs');
    });

    Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
        Route::get('/', function () {
            return view('admin.pos.home');
        })->name('home');

        Route::get('report/daily', [\App\Http\Controllers\Admin\SalesReportController::class, 'dailyIndex'])->name('report.daily');
        Route::get('report/weekly', [\App\Http\Controllers\Admin\SalesReportController::class, 'weeklyIndex'])->name('report.weekly');
        Route::get('report/weekly/detail/{start_date}/{end_date}/{company_id}', [\App\Http\Controllers\Admin\SalesReportController::class, 'showWeekly'])->name('report.weekly.detail');
        Route::get('report/monthly', [\App\Http\Controllers\Admin\SalesReportController::class, 'monthlyIndex'])->name('report.monthly');
        Route::get('report/monthly/detail/{start_date}/{end_date}/{company_id}', [\App\Http\Controllers\Admin\SalesReportController::class, 'showMonthly'])->name('report.monthly.detail');
        Route::resource('report', \App\Http\Controllers\Admin\SalesReportController::class)->except('index');
    });

    Route::group(['prefix' => 'hr', 'as' => 'hr.'], function () {
        Route::get('/', function () {
            return view('admin.hr.home');
        })->name('home');
    });

    Route::group(['prefix' => 'presence', 'as' => 'presence.'], function () {
        //
    });

    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        //
    });
});
