<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Outlet & shift
Route::get('company',[Controllers\API\APIController::class, 'getCompanies'])->name('company.index');
Route::get('shift',[Controllers\API\APIController::class, 'getShifts'])->name('shift.index');

// Menu
Route::get('menu', [Controllers\API\APIController::class, 'getMenu'])->name('menu.index');
Route::post('/menu', [Controllers\API\APIController::class, 'storeMenu'])->name('menu.store');
Route::put('/menu/{id}', [Controllers\API\APIController::class, 'updateMenu'])->name('menu.update');
Route::delete('/menu/{id}', [Controllers\API\APIController::class, 'deleteMenu'])->name('menu.delete');
Route::get('/menu/{id}/stock', [Controllers\API\APIController::class, 'getStockStatus']);
Route::post('/menu/{id}/update-status', [Controllers\API\APIController::class, 'updateStockStatus']);

// Draft
Route::get('/drafts', [Controllers\API\APIController::class, 'getDrafts'])->name('draft.index');
Route::post('/drafts', [Controllers\API\APIController::class, 'createDraft'])->name('draft.create');
Route::delete('/drafts/{id}', [Controllers\API\APIController::class, 'deleteDraft'])->name('draft.delete');
Route::post('/drafts/{id}/items', [Controllers\API\APIController::class, 'addDraftItem'])->name('draft.add-item');
Route::put('/drafts/{draftId}/items/{itemId}', [Controllers\API\APIController::class, 'updateDraftItem'])->name('draft.update-item');
Route::delete('/drafts/{draftId}/items/{itemId}', [Controllers\API\APIController::class, 'deleteDraftItem'])->name('draft.delete-item');

// Cart
Route::get('/cart', [Controllers\API\APIController::class, 'getCart'])->name('cart.get');
Route::post('/cart', [Controllers\API\APIController::class, 'createCart'])->name('cart.create');
Route::put('/cart/{id}', [Controllers\API\APIController::class, 'updateCart'])->name('cart.update');

// Cart items
Route::post('/cart/{id}/items', [Controllers\API\APIController::class, 'addCartItem'])->name('cart.add-item');
Route::put('/cart/{cartId}/items/{itemId}', [Controllers\API\APIController::class, 'updateCartItem'])->name('cart.update-item');
Route::delete('/cart/{cartId}/items/{itemId}', [Controllers\API\APIController::class, 'deleteCartItem'])->name('cart.delete-item');

// Cart discount
Route::post('/cart/{id}/discount', [Controllers\API\APIController::class, 'applyCartDiscount'])->name('cart.apply-discount');
Route::delete('/cart/{id}/discount', [Controllers\API\APIController::class, 'removeCartDiscount'])->name('cart.remove-discount');

// Checkout
Route::post('/cart/{id}/checkout', [Controllers\API\APIController::class, 'checkoutCart'])->name('cart.checkout');

// Move draft to cart
Route::post('/drafts/{id}/to-cart', [Controllers\API\APIController::class, 'moveDraftToCart'])->name('draft.move-to-cart');

Route::get('/trx-number', [Controllers\API\APIController::class, 'generateTrxNumber'])->name('transaction.number');