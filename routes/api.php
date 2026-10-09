<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WorkingOrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Ini adalah route atau "Alamat Endpoint" API kita
// Frontend akan mengakses URL: http://localhost:8000/api/working-orders
Route::get('/working-orders', [WorkingOrderController::class, 'index']);
