<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('company',[API\CompanyController::class, 'index'])->name('company.index');
Route::get('shift',[API\ShiftController::class, 'index'])->name('shift.index');