<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SimulationController;

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
    Route::get('/', 'SimulationController@list'); // Listar todas las simulaciones
    Route::get('/{id}', 'SimulationController@show'); // Mostrar una simulación por ID
    Route::post('/', 'SimulationController@store'); // Crear una nueva simulación
    Route::put('/{id}', 'SimulationController@update'); // Actualizar una simulación por ID
    Route::delete('/{id}', 'SimulationController@destroy'); // Eliminar una simulación por ID
});

Route::prefix('credit-cards')->group(function () {
    Route::get('/', 'CreditCardController@list'); // Listar todas las tarjetas de crédito
    Route::get('/{id}', 'CreditCardController@show'); // Mostrar una tarjeta de crédito por ID
    Route::post('/', 'CreditCardController@store'); // Crear una nueva tarjeta de crédito
    Route::put('/{id}', 'CreditCardController@update'); // Actualizar una tarjeta de crédito por ID
    Route::delete('/{id}', 'CreditCardController@destroy'); // Eliminar una tarjeta de crédito por ID
});

Route::prefix('products')->group(function () {
    Route::get('/', 'ProductController@list'); // Listar todos los productos
    Route::get('/{id}', 'ProductController@show'); // Mostrar un producto por ID
    Route::post('/', 'ProductController@store'); // Crear un nuevo producto
    Route::put('/{id}', 'ProductController@update'); // Actualizar un producto por ID
    Route::delete('/{id}', 'ProductController@destroy'); // Eliminar un producto por ID
});
