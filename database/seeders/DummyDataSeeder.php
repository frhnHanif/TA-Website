<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SensorData;
use App\Models\Cycle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Bersihkan database dengan aman
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SensorData::truncate();
        Cycle::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Kita gunakan Timestamp Murni (Detik) agar perhitungan progres 100% presisi
        $now = Carbon::now();
        $nowTs = $now->getTimestamp();
        
        $startTime = $now->copy()->subDays(30);
        $startTs = $startTime->getTimestamp();

        // ==========================================
        // 2. BUAT DATA SIKLUS (CYCLES)
        // ==========================================
        
        // Siklus 1: Selesai (Durasi 21 Hari)
        $c1StartTs = $startTs;
        $c1EndTs = $c1StartTs + (21 * 24 * 3600); // Tambah 21 hari dalam detik
        
        Cycle::create([
            'batch_id' => '#BCH-' . date('Ym', $c1StartTs) . '-01',
            'start_date' => Carbon::createFromTimestamp($c1StartTs),
            'end_date' => Carbon::createFromTimestamp($c1EndTs),
            'initial_seed_mass' => 50,
            'total_waste_input' => 180.5,
            'harvest_mass' => 28.4,
            'residue_mass' => 45.2,
            'wri_result' => (((180.5 - 45.2) / 180.5) / 21) * 100,
            'eci_result' => (28.4 / 180.5) * 100,
            'status' => 'selesai',
        ]);

        // Siklus 2: Berjalan (Mulai 8 hari yang lalu dari sekarang)
        $c2StartTs = $nowTs - (8 * 24 * 3600); // Kurangi 8 hari dalam detik
        
        Cycle::create([
            'batch_id' => '#BCH-' . date('Ym', $c2StartTs) . '-02',
            'start_date' => Carbon::createFromTimestamp($c2StartTs),
            'end_date' => null,
            'initial_seed_mass' => 50,
            'total_waste_input' => 65.2,
            'harvest_mass' => null,
            'residue_mass' => null,
            'wri_result' => null,
            'eci_result' => null,
            'status' => 'berjalan',
        ]);

        // ==========================================
        // 3. GENERATE DATA SENSOR (Tiap 10 Menit)
        // ==========================================
        $sensorRecords = [];
        $currentTs = $startTs;

        while ($currentTs <= $nowTs) {
            $harvest = 0;
            $baseMass = 0;

            // Logika Pertumbuhan Siklus 1
            if ($currentTs >= $c1StartTs && $currentTs <= $c1EndTs) {
                // Kalkulasi progres dari 0.0 hingga 1.0
                $progress = ($currentTs - $c1StartTs) / (21 * 24 * 3600);
                $baseMass = 8.33 + ($progress * 12258); // Massa tumbuh dari 8.3g ke ~12.2kg

                // 10 menit terakhir Siklus 1 -> Maggot Migrasi (Panen)
                if (($c1EndTs - $currentTs) <= 600) {
                    $harvest = 28.4 * 1000; // Rak panen terisi 28.4 kg (dalam gram)
                    $baseMass = (45.2 * 1000) / 6; // Rak 1-6 menyusut, hanya tersisa Kasgot
                }
            } 
            // Masa Kosong (Jeda antar siklus)
            elseif ($currentTs > $c1EndTs && $currentTs < $c2StartTs) {
                $baseMass = 0;
            } 
            // Logika Pertumbuhan Siklus 2 (Berjalan)
            elseif ($currentTs >= $c2StartTs) {
                $progress = ($currentTs - $c2StartTs) / (21 * 24 * 3600);
                $progress = min(1, $progress); // Pastikan tidak lebih dari 100%
                $baseMass = 8.33 + ($progress * 12258);
            }

            // Fluktuasi noise hanya 2% dari berat aslinya, dibulatkan murni ke integer
            $noise = (int) max(1, $baseMass * 0.02); 
            
            $biopond = [];
            $soil = [];
            for($i=0; $i<6; $i++) {
                if ($baseMass == 0) {
                    $biopond[] = 0;
                } else {
                    $biopond[] = max(0, (int) round($baseMass + rand(-$noise, $noise))); 
                }
                $soil[] = rand(55, 75); // Kelembaban tanah normal 55-75%
            }

            // Simulasi Cuaca (Suhu 28-33C, Hum 60-80%, Amonia 10-35 ppm)
            $temp = round(29.0 + (rand(-15, 20) / 10), 1);
            $hum = round(70.0 + (rand(-60, 60) / 10), 1);
            $ammonia = rand(15, 28);
            
            if (rand(1, 100) > 95) $ammonia = rand(30, 40); // Sesekali gas amonia naik

            // Kembalikan ke format String Datetime untuk database
            $timestampStr = Carbon::createFromTimestamp($currentTs)->format('Y-m-d H:i:s');

            $sensorRecords[] = [
                'biopond' => json_encode($biopond),
                'harvest' => $harvest,
                'temp' => $temp,
                'hum' => $hum,
                'soil' => json_encode($soil),
                'ammonia' => $ammonia,
                'created_at' => $timestampStr,
                'updated_at' => $timestampStr,
            ];

            // Bulk Insert per 500 baris
            if (count($sensorRecords) >= 500) {
                SensorData::insert($sensorRecords);
                $sensorRecords = [];
            }

            // Tambah 10 menit (600 detik)
            $currentTs += 600; 
        }

        if (count($sensorRecords) > 0) {
            SensorData::insert($sensorRecords);
        }
    }
}