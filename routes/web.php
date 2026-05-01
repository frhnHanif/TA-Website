<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\CycleController;

// Halaman Utama
Route::get('/', [SensorDataController::class, 'index']);

// (Nanti Halaman Statistik menyusul di sini)
Route::get('/statistik', [SensorDataController::class, 'statistik']);

// Halaman e-Logbook
Route::get('/logbook', [SensorDataController::class, 'logbook']);

// Halaman Kontrol Aktuator jarak jauh
Route::get('/control', [SensorDataController::class, 'controlPanel']);
Route::post('/web-control', [SensorDataController::class, 'updateControl']);

// Halaman Manajemen Siklus
Route::get('/cycle', [CycleController::class, 'index']);
Route::post('/cycle/start', [CycleController::class, 'store']);
Route::post('/cycle/add-waste', [CycleController::class, 'addWaste'])->name('cycle.addWaste');Route::post('/cycle/finish', [CycleController::class, 'finish']);