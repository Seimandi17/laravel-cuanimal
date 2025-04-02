<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('products', [ProductsController::class, 'index']);
Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'store']);
Route::post('proveedor', [ProveedorController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::apiResource('proveedor', ProveedorController::class)->except(['store']);
    Route::apiResource('categories', CategoriesController::class);
    Route::apiResource('products', ProductsController::class)->except(['index']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::put('validateProvider',[ProveedorController::class, 'validateProvider']);
    Route::get('listProviderPending',[ProveedorController::class, 'listProviderPending']);
    Route::get('listProviderAll',[ProveedorController::class, 'listProviderAll']);
});