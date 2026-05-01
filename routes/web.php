<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\CycleController;

// Rute Publik (Bisa diakses siapa saja tanpa login)
Route::get('/', [SensorDataController::class, 'index'])->name('dashboard');
Route::redirect('/dashboard', '/');
Route::get('/statistik', [SensorDataController::class, 'statistik']);
Route::get('/logbook', [SensorDataController::class, 'logbook']);

// Rute Terproteksi (Hanya bisa diakses oleh Pengelola TPST yang sudah Login)
Route::middleware('auth')->group(function () {
    // Kontrol Aktuator
    Route::get('/control', [SensorDataController::class, 'controlPanel']);
    Route::post('/web-control', [SensorDataController::class, 'updateControl']);

    // Manajemen Siklus
    Route::get('/cycle', [CycleController::class, 'index']);
    Route::post('/cycle/start', [CycleController::class, 'store']);
    Route::post('/cycle/add-waste', [CycleController::class, 'addWaste'])->name('cycle.addWaste');
    Route::post('/cycle/finish', [CycleController::class, 'finish']);
});

require __DIR__.'/auth.php'; // Ini bawaan Laravel Breeze untuk route login/logout
