<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cycle;
use App\Models\SensorData;
use Carbon\Carbon;

class CycleController extends Controller
{
    // 1. Menampilkan halaman Manajemen Siklus & Prediksi ADD
    public function index()
    {
        $activeCycle = Cycle::where('status', 'berjalan')->first();
        $finishedCycles = Cycle::where('status', 'selesai')->orderBy('end_date', 'desc')->get();

        $avgTemp = 0;
        $avgHum = 0;
        
        // Variabel Tambahan untuk Model Thermal ADD
        $accumulatedADD = 0;
        $targetADD = 500; // Batas ambang thermal target BSF untuk siap panen (prepupa)
        $addProgress = 0;
        $estimatedRemainingDays = '-';
        
        $latestSensor = SensorData::latest()->first(); // Ambil data sensor paling mutakhir
        
        if ($activeCycle) {
            $sensorHistory = SensorData::where('created_at', '>=', $activeCycle->start_date)->get();
            
            if ($sensorHistory->count() > 0) {
                $avgTemp = round($sensorHistory->avg('temp'), 1);
                $avgHum = round($sensorHistory->avg('hum'), 1);

                // --- LOGIKA HITUNG MODEL ACCUMULATED DEGREE DAYS (ADD) ---
                // Kelompokkan log sensor berdasarkan tanggal pengerjaan
                $perDayLogs = $sensorHistory->groupBy(function($data) {
                    return Carbon::parse($data->created_at)->format('Y-m-d');
                });

                foreach ($perDayLogs as $date => $logs) {
                    $dailyAvgTemp = $logs->avg('temp');
                    
                    // 15°C adalah Batas Suhu Dasar (Base Temperature) Biologis Maggot BSF
                    if ($dailyAvgTemp > 15) { 
                        $accumulatedADD += ($dailyAvgTemp - 15);
                    }
                }
                
                $accumulatedADD = round($accumulatedADD, 1);
                
                // Hitung Persentase Progress Kematangan Larva
                $addProgress = min(round(($accumulatedADD / $targetADD) * 100, 1), 100);
                
                // Hitung Estimasi Sisa Hari Menuju Panen
                $avgDailyADD = $avgTemp - 15;
                if ($avgDailyADD > 0 && $accumulatedADD < $targetADD) {
                    $remainingADD = $targetADD - $accumulatedADD;
                    $estimatedRemainingDays = max(1, ceil($remainingADD / $avgDailyADD));
                } else {
                    $estimatedRemainingDays = 0; // Nilai 0 mengindikasikan fase Prepupa / Siap Panen
                }
            }
        }

        return view('cycle.index', compact(
            'activeCycle', 
            'finishedCycles', 
            'avgTemp', 
            'avgHum', 
            'latestSensor', 
            'accumulatedADD', 
            'targetADD', 
            'addProgress', 
            'estimatedRemainingDays'
        ));
    }

    // 2. Aksi: Memulai Siklus Baru
    public function store(Request $request)
    {
        // Bibit awal tetap manual, karena 50 gram terlalu ringan untuk dibaca akurat oleh Load Cell 50kg
        $request->validate([
            'bibit_awal' => 'required|numeric|min:0',
        ]);

        if (Cycle::where('status', 'berjalan')->exists()) {
            return back()->with('error', 'Gagal! Masih ada siklus yang sedang berjalan.');
        }

        $batchId = '#BCH-' . now()->format('Ym') . '-' . str_pad(Cycle::count() + 1, 2, '0', STR_PAD_LEFT);

        // SNAPSHOT PAKAN AWAL: Jika operator tidak mengisi manual, ambil dari pembacaan Load Cell saat ini (Rak 1-6)
        $pakanAwal = $request->pakan_awal;
        if (empty($pakanAwal)) {
            $latestSensor = SensorData::latest()->first();
            if ($latestSensor) {
                $biopondArray = is_array($latestSensor->biopond) ? $latestSensor->biopond : json_decode($latestSensor->biopond, true) ?? [];
                $pakanAwal = array_sum($biopondArray) / 1000; // Convert gram to kg
            } else {
                $pakanAwal = 0;
            }
        }

        Cycle::create([
            'batch_id' => $batchId,
            'start_date' => now(),
            'initial_seed_mass' => $request->bibit_awal,
            'total_waste_input' => $pakanAwal,
            'status' => 'berjalan'
        ]);

        return back()->with('success', 'Siklus budidaya baru berhasil dimulai!');
    }

    // 3. Aksi: Menambah Catatan Pakan Manual
    public function addWaste(Request $request)
    {
        // Validasi bahwa input pakan_rak adalah sebuah array
        $request->validate([
            'pakan_rak' => 'required|array',
            'pakan_rak.*' => 'nullable|numeric|min:0',
        ]);

        $cycle = Cycle::where('status', 'berjalan')->first();
        
        if ($cycle) {
            // Hitung total tambahan pakan dari seluruh rak yang diisi
            $totalTambahan = 0;
            foreach ($request->pakan_rak as $pakan) {
                if (!empty($pakan)) {
                    $totalTambahan += (float) $pakan;
                }
            }

            // Jika ada pakan yang ditambahkan, update database
            if ($totalTambahan > 0) {
                $cycle->total_waste_input += $totalTambahan;
                $cycle->save();
                
                return back()->with('success', "Berhasil! Total " . $totalTambahan . " kg pakan ditambahkan ke dalam siklus.");
            } else {
                return back()->with('error', 'Tidak ada data pakan yang dimasukkan.');
            }
        }

        return back()->with('error', 'Gagal! Tidak ada siklus yang sedang berjalan.');
    }

    // 4. Aksi: Akhiri Siklus (SANGAT OTOMATIS)
    public function finish(Request $request)
    {
        $cycle = Cycle::where('status', 'berjalan')->first();
        
        if (!$cycle) {
            return back()->with('error', 'Tidak ada siklus yang sedang berjalan.');
        }

        // SNAPSHOT PANEN: Ambil data sensor pada detik tombol "Panen" ditekan
        $latestSensor = SensorData::latest()->first();

        if (!$latestSensor) {
            return back()->with('error', 'Data sensor tidak ditemukan. Pastikan ESP32 aktif.');
        }

        // Jika user mengisi manual di form pop-up, gunakan itu. 
        // Jika form kosong (auto), gunakan data dari Sensor ESP32.
        
        // Kasgot = Sisa di Rak 1 - 6
        $kasgot = $request->kasgot_aktual;
        if (empty($kasgot)) {
            $biopondArray = is_array($latestSensor->biopond) ? $latestSensor->biopond : json_decode($latestSensor->biopond, true) ?? [];
            $kasgot = array_sum($biopondArray) / 1000; // kg
        }

        // Panen Prepupa = Data di Rak 7 (harvest)
        $panen = $request->panen_aktual;
        if (empty($panen)) {
            $panen = $latestSensor->harvest; // Asumsi kolom 'harvest' di DB sudah dalam kg. Jika gram, bagi 1000.
        }

        $input = $cycle->total_waste_input;
        $days = $cycle->days_elapsed;

        // RUMUS 1: ECI -> (Panen / Input Sampah) * 100%
        $eci = ($input > 0) ? ($panen / $input) * 100 : 0;

        // RUMUS 2: WRI -> ((Input - Kasgot) / Input) / Durasi Hari * 100%
        // Bu Vivi's standard formula representation:
        $wri = ($input > 0 && $days > 0) ? ((($input - $kasgot) / $input) / $days) * 100 : 0;

        $cycle->update([
            'end_date' => now(),
            'harvest_mass' => $panen,
            'residue_mass' => $kasgot,
            'eci_result' => $eci,
            'wri_result' => $wri,
            'status' => 'selesai'
        ]);

        return back()->with('success', 'Siklus berhasil dipanen! Data aktual otomatis ditarik dari sensor Load Cell.');
    }
}