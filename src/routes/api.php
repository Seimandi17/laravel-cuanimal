<?php

use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('proveedor', ProveedorController::class);
Route::apiResource('users', UserController::class);

