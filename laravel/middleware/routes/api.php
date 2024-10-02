<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrestashopController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/prestashop/product/{id}', [PrestashopController::class, 'getProduct']);

Route::post('/prestashop/product', [PrestashopController::class, 'createProduct']);
