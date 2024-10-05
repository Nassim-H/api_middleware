<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrestashopController;
use App\Http\Controllers\OdooController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/prestashop/product/{id}', [PrestashopController::class, 'getProduct']);

Route::post('/prestashop/product', [PrestashopController::class, 'createProduct']);

Route::get('/odoo/test-auth', [OdooController::class, 'testAuth']);


Route::get('/odoo/products', [OdooController::class, 'getProducts']);
