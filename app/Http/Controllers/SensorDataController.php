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
            'is_manual' => $control->is_manual,
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

    // 4. Halaman Statistik & Analitik
    public function statistik()
    {
        // Ambil 50 data terbaru, lalu balik urutannya agar grafik berjalan dari kiri (lama) ke kanan (baru)
        $sensorHistory = SensorData::latest()->take(50)->get()->reverse()->values();

        // Siapkan Label Waktu (Sumbu X)
        $timestamps = $sensorHistory->pluck('created_at')->map(function($date) {
            return $date->format('H:i');
        });

        // Ekstrak data untuk Sumbu Y
        $tempData = $sensorHistory->pluck('temp');
        $humData = $sensorHistory->pluck('hum');
        $ammoniaData = $sensorHistory->pluck('ammonia');
        
        // Hitung Tren Total Massa Biopond
        $totalMassData = $sensorHistory->map(function($item) {
            $biopond = is_array($item->biopond) ? $item->biopond : json_decode($item->biopond, true) ?? [];
            return array_sum($biopond) / 1000; // Konversi ke kg
        });

        // Ambil data spesifik rak dari data paling mutakhir untuk grafik Bar
        $latestData = $sensorHistory->last();
        $latestBiopond = $latestData ? (is_array($latestData->biopond) ? $latestData->biopond : json_decode($latestData->biopond, true)) : [0,0,0,0,0,0];
        $latestSoil = $latestData ? (is_array($latestData->soil) ? $latestData->soil : json_decode($latestData->soil, true)) : [0,0,0,0,0,0];

        // Total Panen (Akumulasi keseluruhan dari database)
        $totalHarvest = SensorData::sum('harvest');

        // Memperbaiki view ke folder 'statistik' agar sesuai
        return view('statistik.index', compact(
            'timestamps', 'tempData', 'humData', 'ammoniaData', 'totalMassData', 
            'latestBiopond', 'latestSoil', 'totalHarvest'
        ));
    }

    // =========================================================================
    // KELOMPOK 3: AKSI DARI HALAMAN WEB (WEB ACTIONS & API FETCH)
    // =========================================================================

    // Method KHUSUS WEB untuk melayani Request AJAX (Filter Harian, Mingguan, Bulanan, Tahunan)
    public function getStatisticsData(Request $request)
    {
        $period = $request->query('period', 'daily');
        
        if ($period === 'daily') {
            // Harian: Dikelompokkan per JAM (misal: 08:00, 09:00)
            $sensors = SensorData::where('created_at', '>=', now()->subDay())
                ->orderBy('created_at', 'asc')->get()
                ->groupBy(function($date) { return \Carbon\Carbon::parse($date->created_at)->format('H:00'); });
        } elseif ($period === 'weekly') {
            // Mingguan: Dikelompokkan per HARI (misal: 12 Okt, 13 Okt)
            $sensors = SensorData::where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'asc')->get()
                ->groupBy(function($date) { return \Carbon\Carbon::parse($date->created_at)->format('d M'); });
        } elseif ($period === 'monthly') {
            // Bulanan: Dikelompokkan per HARI (misal: 12 Okt, 13 Okt)
            $sensors = SensorData::where('created_at', '>=', now()->subDays(30))
                ->orderBy('created_at', 'asc')->get()
                ->groupBy(function($date) { return \Carbon\Carbon::parse($date->created_at)->format('d M'); });
        } else {
            // Tahunan: Dikelompokkan per BULAN (misal: Jan 2026, Feb 2026)
            $sensors = SensorData::where('created_at', '>=', now()->subYear())
                ->orderBy('created_at', 'asc')->get()
                ->groupBy(function($date) { return \Carbon\Carbon::parse($date->created_at)->format('M Y'); });
        }

        // Siapkan penampung data
        $timestamps = []; $tempData = []; $humData = []; 
        $ammoniaData = []; $totalMassData = [];

        // Hitung nilai RATA-RATA untuk setiap kelompok waktu (Jam / Hari)
        foreach ($sensors as $timeLabel => $dataGroup) {
            $timestamps[] = $timeLabel;
            $tempData[] = round($dataGroup->avg('temp'), 1);
            $humData[] = round($dataGroup->avg('hum'), 1);
            $ammoniaData[] = round($dataGroup->avg('ammonia'), 1);
            
            // Hitung rata-rata total massa di hari/jam tersebut
            $avgMass = $dataGroup->map(function($item) {
                $biopond = is_array($item->biopond) ? $item->biopond : json_decode($item->biopond, true) ?? [];
                return array_sum($biopond) / 1000;
            })->avg();
            
            $totalMassData[] = round($avgMass, 2);
        }

        return response()->json([
            'timestamps' => $timestamps,
            'tempData' => $tempData,
            'humData' => $humData,
            'ammoniaData' => $ammoniaData,
            'totalMassData' => $totalMassData,
        ]);
    }

    // Method KHUSUS WEB untuk menerima klik tombol dan menyimpannya ke DB
    public function updateControl(Request $request)
    {
        $control = DeviceControl::first();

        // Tangkap perubahan Mode (Manual/Auto)
        if ($request->has('is_manual')) {
            $control->is_manual = $request->is_manual;
        }

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