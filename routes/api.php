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

Route::get('/refresh-csrf', [App\Http\Controllers\Api\APIController::class, 'refreshCsrfToken'])->middleware('auth');

Route::get('/drafts', [API\APIController::class, 'getDrafts'])->name('draft.index');
Route::post('/drafts', [API\APIController::class, 'createDraft'])->name('draft.create');
Route::delete('/drafts/{id}', [API\APIController::class, 'deleteDraft'])->name('draft.delete');
Route::post('/drafts/{id}/to-cart', [API\APIController::class, 'moveDraftToCart'])->name('draft.move');
Route::post('/drafts/{id}/items', [API\APIController::class, 'addDraftItem'])->name('draft.add-item');
Route::put('/drafts/{draftId}/items/{itemId}', [API\APIController::class, 'updateDraftItem'])->name('draft.update-item');
Route::delete('/drafts/{draftId}/items/{itemId}', [API\APIController::class, 'deleteDraftItem'])->name('draft.delete-item');