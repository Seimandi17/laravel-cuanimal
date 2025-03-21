<?php

use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::apiResource('proveedor', ProveedorController::class);
    Route::post('logout', [UserController::class, 'logout']);
});