@extends('layouts.app')

@section('title', 'Dashboard SiMaggot')

@section('content')
    @if($latestData)
        <!-- ========================================== -->
        <!-- BAGIAN 1: KPI UTAMA (TOP CARDS)            -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-6 mb-6">
            
            <!-- Suhu Udara -->
            <div class="bg-white rounded-[1.5rem] shadow-sm p-5 border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-temperature-half"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-0.5">Suhu Udara</p>
                    <p class="text-2xl font-black text-gray-800">{{ $latestData->temp }} <span class="text-sm font-medium text-gray-500">°C</span></p>
                </div>
            </div>

            <!-- Kelembaban Udara -->
            <div class="bg-white rounded-[1.5rem] shadow-sm p-5 border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-droplet"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-0.5">Kelembaban</p>
                    <p class="text-2xl font-black text-gray-800">{{ $latestData->hum }} <span class="text-sm font-medium text-gray-500">%</span></p>
                </div>
            </div>

            <!-- Kelembaban Tanah (Avg) -->
            <div class="bg-white rounded-[1.5rem] shadow-sm p-5 border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-seedling"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-0.5">Tanah (Avg)</p>
                    @php
                        $soilArray = is_array($latestData->soil) ? $latestData->soil : json_decode($latestData->soil, true) ?? [];
                        $avgSoil = count($soilArray) > 0 ? round(array_sum($soilArray) / count($soilArray), 1) : 0;
                    @endphp
                    <p class="text-2xl font-black text-gray-800">{{ $avgSoil }} <span class="text-sm font-medium text-gray-500">%</span></p>
                </div>
            </div>

            <!-- Amonia -->
            <div class="bg-white rounded-[1.5rem] shadow-sm p-5 border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-full {{ $latestData->ammonia > 30 ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500' }} flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-biohazard"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-0.5">Amonia</p>
                    <p class="text-2xl font-black {{ $latestData->ammonia > 30 ? 'text-red-600' : 'text-gray-800' }}">{{ $latestData->ammonia }} <span class="text-sm font-medium text-gray-500">ppm</span></p>
                </div>
            </div>

            <!-- Massa Total -->
            <div class="bg-white rounded-[1.5rem] shadow-sm p-5 border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-weight-hanging"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-0.5">Massa Total</p>
                    @php
                        $biopondArray = is_array($latestData->biopond) ? $latestData->biopond : json_decode($latestData->biopond, true) ?? [];
                        $totalBerat = array_sum($biopondArray);
                    @endphp
                    <p class="text-2xl font-black text-gray-800">{{ number_format($totalBerat / 1000, 2) }} <span class="text-sm font-medium text-gray-500">kg</span></p>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN 2: DETAIL BIOPOND (MIDDLE)          -->
        <!-- ========================================== -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-5 sm:p-6 mb-6">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold text-gray-800">Status Rak Biopond</h3>
                <span class="text-xs font-bold bg-gray-100 text-gray-500 px-3 py-1 rounded-full"><i class="fa-regular fa-clock mr-1"></i> {{ $latestData->created_at->diffForHumans() }}</span>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                @foreach($biopondArray as $index => $berat)
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 hover:border-amber-400 transition-colors group">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Rak {{ $index + 1 }}</span>
                            <i class="fa-solid fa-layer-group text-gray-300 group-hover:text-amber-400 transition-colors"></i>
                        </div>
                        
                        <!-- Massa (Desimal diperbaiki pakai round) -->
                        <div class="mb-3">
                            <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Massa Maggot</span>
                            <div class="flex items-baseline gap-1">
                                <span class="text-xl font-black text-gray-800">{{ number_format(round($berat), 0, ',', '.') }}</span>
                                <span class="text-xs text-gray-500 font-medium">g</span>
                            </div>
                        </div>

                        <!-- Kelembaban Tanah -->
                        <div>
                            <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Kelembaban Tanah</span>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-seedling text-amber-500 text-xs"></i>
                                <span class="text-sm font-bold text-gray-700">{{ $soilArray[$index] ?? '--' }} %</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1 mt-1.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-1 rounded-full" style="width: {{ $soilArray[$index] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN 3: BENTO PREVIEWS (BOTTOM)          -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Kiri: Preview Statistik (Desain Gelap/Dark ala Debtrix dengan Data Analitik) -->
            <a href="/statistik" class="block col-span-1 bg-gray-900 rounded-[1.5rem] shadow-sm p-6 text-white hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group flex flex-col justify-between">
                <!-- Elemen dekorasi bg -->
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-500 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                
                <div class="flex justify-between items-start mb-6 relative z-10">
                    <div>
                        <h3 class="text-lg font-bold">Analitik Lanjutan</h3>
                        <p class="text-xs text-gray-400 mt-1">Ringkasan performa sistem</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm shrink-0">
                        <i class="fa-solid fa-arrow-right -rotate-45 group-hover:rotate-0 transition-transform"></i>
                    </div>
                </div>

                <div class="space-y-4 relative z-10">
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Total Akumulasi Panen</p>
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-8 rounded-full bg-green-500"></div>
                            <span class="text-3xl font-black">{{ isset($totalHarvest) ? number_format($totalHarvest / 1000, 2) : '0.00' }} <span class="text-lg font-medium text-gray-400">kg</span></span>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Estimasi Waste Reduction (WRI)</p>
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-8 rounded-full bg-blue-500"></div>
                            <span class="text-3xl font-black">68.5 <span class="text-lg font-medium text-gray-400">%</span></span>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-800">
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-1">Efisiensi Biokonversi (ECI)</p>
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-8 rounded-full bg-purple-500"></div>
                            <span class="text-2xl font-black text-amber-400">18.2 <span class="text-base font-medium text-amber-200/50">%</span></span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 w-full bg-white/10 hover:bg-white/20 text-center py-2.5 rounded-xl text-sm font-bold transition-colors backdrop-blur-sm">
                    Lihat Detail Analitik
                </div>
            </a>

            <!-- Kanan: Preview Logbook -->
            <div class="col-span-1 lg:col-span-2 bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-6 flex flex-col relative">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Logbook Terbaru</h3>
                        <p class="text-xs text-gray-500 mt-0.5">5 Pembacaan telemetri terakhir dari ESP32</p>
                    </div>
                    <a href="/logbook" class="bg-gray-50 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold transition-colors border border-gray-200 flex items-center gap-2">
                        <span>Lihat Semua</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider pl-2">Waktu</th>
                                <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Suhu</th>
                                <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Hum Udara</th>
                                <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Hum Tanah (Avg)</th>
                                <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Amonia</th>
                                <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider text-right pr-2">Total Massa Maggot</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse($recentLogs as $log)
                                @php
                                    // Hitung Total Massa Maggot
                                    $biopondArray = is_array($log->biopond) ? $log->biopond : json_decode($log->biopond, true) ?? [];
                                    $totalBerat = array_sum($biopondArray) / 1000;
                                    
                                    // Hitung Rata-rata Kelembaban Tanah
                                    $soilArray = is_array($log->soil) ? $log->soil : json_decode($log->soil, true) ?? [];
                                    $avgSoil = count($soilArray) > 0 ? array_sum($soilArray) / count($soilArray) : 0;
                                @endphp
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors group">
                                    <td class="py-3 pl-2 font-medium text-gray-800">{{ $log->created_at->format('H:i') }} <span class="text-xs text-gray-400 ml-1">{{ $log->created_at->format('d/m') }}</span></td>
                                    <td class="py-3 text-gray-600">{{ $log->temp }} °C</td>
                                    <td class="py-3 text-gray-600">{{ $log->hum }} %</td>
                                    <td class="py-3 text-gray-600">{{ number_format($avgSoil, 1) }} %</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-md {{ $log->ammonia > 30 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $log->ammonia }} ppm
                                        </span>
                                    </td>
                                    <td class="py-3 text-right pr-2 font-bold text-gray-700">
                                        {{ number_format($totalBerat, 2) }} kg
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400 text-sm">Belum ada data terekam.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @else
        <!-- State Kosong jika database benar-benar kosong -->
        <div class="bg-amber-50 border border-amber-200 rounded-[1.5rem] p-8 text-center max-w-lg mx-auto mt-10">
            <div class="w-20 h-20 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fa-solid fa-satellite-dish"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Menunggu Data Pertama...</h2>
            <p class="text-gray-500 text-sm">Sistem belum menerima paket telemetri apapun dari ESP32. Pastikan perangkat hardware sudah menyala dan terhubung ke internet.</p>
        </div>
    @endif
@endsection