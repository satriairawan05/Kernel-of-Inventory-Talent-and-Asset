<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('company',[API\APIController::class, 'getCompanies'])->name('company.index');
Route::get('shift',[API\APIController::class, 'getShifts'])->name('shift.index');
Route::get('menu', [API\APIController::class, 'getMenu'])->name('menu.index');