<?php

use App\Http\Controllers\FlaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrestashopController;
use App\Http\Controllers\OdooController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['App\Http\Middleware\VerifyApiKey'])->group(function () {
    Route::post('/prestashop/products', [PrestashopController::class, 'createProduct']);
    Route::post('/odoo/products', [FlaskController::class, 'createProductInOdoo']);
    Route::get('/products/sync-to-odoo/{id}', [PrestashopController::class, 'syncProductToOdoo']);
    Route::get('/products/sync-to-prestashop/{id}', [PrestashopController::class, 'syncProductFromOdooToPrestashop']);
});

Route::get('/prestashop/products', [PrestashopController::class, 'listAllProducts']);
Route::get('/prestashop/products/{id}', [PrestashopController::class, 'getProduct']);
Route::get('/odoo/products', [FlaskController::class, 'listAllProducts']);
Route::get('/odoo/products/{id}', [FlaskController::class, 'getProduct']);

