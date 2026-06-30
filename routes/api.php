<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Outlet & shift
Route::get('company',[API\APIController::class, 'getCompanies'])->name('company.index');
Route::get('shift',[API\APIController::class, 'getShifts'])->name('shift.index');

// Menu
Route::get('menu', [API\APIController::class, 'getMenu'])->name('menu.index');
Route::post('/menu', [API\APIController::class, 'storeMenu'])->name('menu.store');
Route::put('/menu/{id}', [API\APIController::class, 'updateMenu'])->name('menu.update');
Route::delete('/menu/{id}', [API\APIController::class, 'deleteMenu'])->name('menu.delete');

// CSRF
Route::get('/refresh-csrf', [App\Http\Controllers\Api\APIController::class, 'refreshCsrfToken'])->middleware('auth');

// Draft
Route::get('/drafts', [API\APIController::class, 'getDrafts'])->name('draft.index');
Route::post('/drafts', [API\APIController::class, 'createDraft'])->name('draft.create');
Route::delete('/drafts/{id}', [API\APIController::class, 'deleteDraft'])->name('draft.delete');
Route::post('/drafts/{id}/items', [API\APIController::class, 'addDraftItem'])->name('draft.add-item');
Route::put('/drafts/{draftId}/items/{itemId}', [API\APIController::class, 'updateDraftItem'])->name('draft.update-item');
Route::delete('/drafts/{draftId}/items/{itemId}', [API\APIController::class, 'deleteDraftItem'])->name('draft.delete-item');

// Cart
Route::get('/cart', [API\APIController::class, 'getCart'])->name('cart.get');
Route::post('/cart', [API\APIController::class, 'createCart'])->name('cart.create');
Route::put('/cart/{id}', [API\APIController::class, 'updateCart'])->name('cart.update');

// Cart items
Route::post('/cart/{id}/items', [API\APIController::class, 'addCartItem'])->name('cart.add-item');
Route::put('/cart/{cartId}/items/{itemId}', [API\APIController::class, 'updateCartItem'])->name('cart.update-item');
Route::delete('/cart/{cartId}/items/{itemId}', [API\APIController::class, 'deleteCartItem'])->name('cart.delete-item');

// Cart discount
Route::post('/cart/{id}/discount', [API\APIController::class, 'applyCartDiscount'])->name('cart.apply-discount');
Route::delete('/cart/{id}/discount', [API\APIController::class, 'removeCartDiscount'])->name('cart.remove-discount');

// Checkout
Route::post('/cart/{id}/checkout', [API\APIController::class, 'checkoutCart'])->name('cart.checkout');

// Move draft to cart
Route::post('/drafts/{id}/to-cart', [API\APIController::class, 'moveDraftToCart'])->name('draft.move-to-cart');