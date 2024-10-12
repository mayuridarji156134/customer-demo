<?php
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;

Route::get('customers', [CustomerController::class, 'index']);
Route::post('customers', [CustomerController::class, 'store']);
Route::get('customers/{customer}', [CustomerController::class, 'show']);
Route::put('customers/{customer}', [CustomerController::class, 'update']);
Route::delete('customers/{customer}', [CustomerController::class, 'destroy']);

Route::get('categories', [CategoryController::class, 'index']);

Route::get('customers/{customer}/contacts', [ContactController::class, 'index']);
Route::post('customers/{customer}/contacts', [ContactController::class, 'store']);
Route::get('contacts/{contact}', [ContactController::class, 'show']);
Route::put('contacts/{contact}', [ContactController::class, 'update']);
Route::delete('contacts/{contact}', [ContactController::class, 'destroy']);
