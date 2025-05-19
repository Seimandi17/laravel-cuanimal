<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ContactoGeneralController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\PedidoTransporteController;
use App\Http\Controllers\ProveedorContactoController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RecorridoController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\HistorialController;
use Illuminate\Support\Facades\Route;


Route::get('products', [ProductsController::class, 'index']);
Route::get('products/{id}', [ProductsController::class, 'show']);
Route::post('send-message', [MessageController::class, 'sendMessage']);
Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'store']);
Route::post('proveedor', [ProveedorController::class, 'store']);
Route::post('/contacto-general', [ContactoGeneralController::class, 'enviar']);
Route::post('/contacto', [ContactoController::class, 'enviar']);
Route::post('contacto-proveedor', [ProveedorContactoController::class, 'enviar']);
Route::post('/pedidos-transporte', [PedidoTransporteController::class, 'store']);
Route::get('/pedidos-transporte', [PedidoTransporteController::class, 'index']);
Route::get('/blog-recorrido', [RecorridoController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::apiResource('proveedor', ProveedorController::class)->except(['store']);
    Route::apiResource('categories', CategoriesController::class);
    Route::apiResource('products', ProductsController::class)->except(['index']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::put('validateProvider',[ProveedorController::class, 'validateProvider']);
    Route::get('listProviderPending',[ProveedorController::class, 'listProviderPending']);
    Route::get('listProviderAll',[ProveedorController::class, 'listProviderAll']);
    Route::post('/blog-recorrido', [RecorridoController::class, 'store']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::patch('/reservations/{id}', [ReservationController::class, 'update']); 
    Route::get('/historial', [HistorialController::class, 'index']);
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{id}', [ConversationController::class, 'show']);
    Route::post('/conversations/{id}/messages', [ConversationController::class, 'sendMessage']);
    Route::delete('/conversations/{id}', [ConversationController::class, 'destroy']);
    Route::get('/admin/conversations', [ConversationController::class, 'adminIndex']);
    Route::get('/admin/unassigned-conversations', [ConversationController::class, 'unassigned']);
    Route::post('/admin/conversations/{id}/assign', [ConversationController::class, 'assign']);
});
