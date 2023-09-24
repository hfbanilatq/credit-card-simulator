<?php

use App\Http\Controllers\Api\CreditCardController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SimulationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('simulations')->group(function () {
    Route::get('/', [SimulationController::class, 'list']); // Listar todas las simulaciones
    Route::get('/{id}', [SimulationController::class, 'show']); // Mostrar una simulación por ID
    Route::post('/', [SimulationController::class, 'store']); // Crear una nueva simulación
    Route::put('/{id}', [SimulationController::class, 'update']); // Actualizar una simulación por ID
    Route::delete('/{id}', [SimulationController::class, 'destroy']);
    Route::post('/simulate', [SimulationController::class, 'simulate']);
});

Route::prefix('credit-cards')->group(function () {
    Route::get('/', [CreditCardController::class, 'list']); // Listar todas las tarjetas de crédito
    Route::get('/{id}', [CreditCardController::class, 'show']); // Mostrar una tarjeta de crédito por ID
    Route::post('/', [CreditCardController::class, 'store']); // Crear una nueva tarjeta de crédito
    Route::put('/{id}', [CreditCardController::class, 'update']); // Actualizar una tarjeta de crédito por ID
    Route::delete('/{id}', [CreditCardController::class, 'delete']); // Eliminar una tarjeta de crédito por ID
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class,'list']); // Listar todos los productos
    Route::get('/{id}', [ProductController::class,'show']); // Mostrar un producto por ID
    Route::post('/', [ProductController::class,'store']); // Crear un nuevo producto
    Route::put('/{id}', [ProductController::class,'update']); // Actualizar un producto por ID
    Route::delete('/{id}', [ProductController::class,'destroy']); // Eliminar un producto por ID
});
