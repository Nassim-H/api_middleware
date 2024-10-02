<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrestashopController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prestashop/product/{id}', [PrestashopController::class, 'getProduct']);


