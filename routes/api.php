<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/sensor', [SensorDataController::class, 'store']);
Route::get('/control', [SensorDataController::class, 'getControl']);
Route::post('/control', [SensorDataController::class, 'updateControl']);
