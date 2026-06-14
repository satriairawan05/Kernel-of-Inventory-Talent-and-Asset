<?php

use App\Http\Controllers\Admin\SalesReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    });

    Route::group(['prefix' => 'inventory', 'as' => 'inventory.'], function () {
        Route::get('/', function () {
            return view('admin.inventory.home');
        })->name('home');

        Route::resource('category', \App\Http\Controllers\Admin\CategoryController::class);
    });

    Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
        Route::get('/', function () {
            return view('admin.pos.home');
        })->name('home');

        Route::get('report/daily', [SalesReportController::class, 'dailyIndex'])->name('report.daily');
        Route::get('report/weekly', [SalesReportController::class, 'weeklyIndex'])->name('report.weekly');
        Route::get('report/weekly/detail/{start_date}/{end_date}/{company_id?}', [SalesReportController::class, 'showWeekly'])->name('report.weekly.detail');
        Route::get('report/monthly', [SalesReportController::class, 'monthlyIndex'])->name('report.monthly');
        Route::get('report/monthly/detail/{start_date}/{end_date}/{company_id?}', [SalesReportController::class, 'showMonthly'])->name('report.monthly.detail');
        Route::resource('report', SalesReportController::class)->except('index');
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
