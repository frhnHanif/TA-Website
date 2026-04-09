<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\DeviceControl;

class SensorDataController extends Controller
{
    // Method untuk menerima data dari ESP32
    public function store(Request $request)
    {
        // Menyimpan data langsung (karena key request sudah sesuai dengan kolom DB)
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

    // Mengirimkan JSON status kontrol ke ESP32
    public function getControl()
    {
        // Ambil data baris pertama (satu-satunya data kontrol kita)
        $control = DeviceControl::first();
        
        return response()->json([
            'mist' => $control->mist,
            'fan' => $control->fan
        ]);
    }

    
    // Method untuk menampilkan data di Web
    public function index()
    {
        // Ambil 20 data terbaru
        $sensors = SensorData::latest()->take(20)->get();
        // Ambil status alat saat ini untuk ditampilkan di tombol web
        $control = DeviceControl::first(); 
        
        return view('sensor.index', compact('sensors', 'control'));
    }

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
