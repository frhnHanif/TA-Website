<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;

// Halaman Utama
Route::get('/', [SensorDataController::class, 'index']);

// (Nanti Halaman Statistik menyusul di sini)

// Halaman e-Logbook
Route::get('/logbook', [SensorDataController::class, 'logbook']);

// Halaman Kontrol Aktuator jarak jauh
Route::get('/control', [SensorDataController::class, 'controlPanel']);
Route::post('/web-control', [SensorDataController::class, 'updateControl']);