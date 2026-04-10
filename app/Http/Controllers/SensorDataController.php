<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\DeviceControl;

class SensorDataController extends Controller
{
    // =========================================================================
    // KELOMPOK 1: API UNTUK HARDWARE (ESP32)
    // =========================================================================

    // Menerima data sensor dari ESP32 (POST)
    public function store(Request $request)
    {
        $data = SensorData::create([
            'biopond' => $request->biopond,
            'harvest' => $request->harvest,
            'temp' => $request->temp,
            'hum' => $request->hum,
            'soil' => $request->soil,
            'ammonia' => $request->ammonia,
        ]);

        return response()->json(['message' => 'Data berhasil disimpan', 'data' => $data], 201);
    }

    // Mengirimkan status kontrol terbaru ke ESP32 (GET)
    public function getControl()
    {
        $control = DeviceControl::first();
        
        return response()->json([
            'mist' => $control->mist,
            'fan' => $control->fan
        ]);
    }

    // =========================================================================
    // KELOMPOK 2: MENAMPILKAN HALAMAN WEB (VIEWS)
    // =========================================================================

    // 1. Halaman Utama (Summary Kondisi Terkini)
    public function index()
    {
        // Ambil 1 data paling baru untuk summary ringkasan
        $latestData = SensorData::latest()->first(); 
        return view('dashboard.index', compact('latestData'));
    }

    // 2. Halaman e-Logbook (Riwayat Data Tabel)
    public function logbook()
    {
        // Pakai paginate(20) agar halaman ada navigasi Next/Prev
        $sensors = SensorData::latest()->paginate(20); 
        return view('logbook.index', compact('sensors'));
    }

    // 3. Halaman Kontrol Aktuator Jarak Jauh
    public function controlPanel()
    {
        // Ambil status terkini untuk ditampilkan di tombol web
        $control = DeviceControl::first();
        // Ambil data sensor terbaru untuk panduan manual override
        $latestData = SensorData::latest()->first(); 
        
        return view('control.index', compact('control', 'latestData'));
    }

    // =========================================================================
    // KELOMPOK 3: AKSI DARI HALAMAN WEB (WEB ACTIONS)
    // =========================================================================

    // Method KHUSUS WEB untuk menerima klik tombol dan menyimpannya ke DB
    public function updateControl(Request $request)
    {
        $control = DeviceControl::first();

        // Jika user menggeser slider kipas
        if ($request->has('fan')) {
            $control->fan = $request->fan;
        }

        // Jika user menekan tombol mist maker
        if ($request->has('mist_index') && $request->has('mist_value')) {
            $mistArray = $control->mist;
            $mistArray[$request->mist_index] = (int) $request->mist_value;
            $control->mist = $mistArray; // Simpan kembali array yang sudah diubah
        }

        $control->save();
        return response()->json(['message' => 'Status berhasil diupdate!']);
    }
}