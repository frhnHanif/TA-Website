@extends('layouts.app')

@section('title', 'Ringkasan Kondisi Biopond')

@section('content')
    @if($latestData)
        <!-- Kartu Indikator Utama -->
        <!-- Mengubah grid menjadi 3 kolom di layar besar agar 5 item terlihat rapi (3 di atas, 2 di bawah) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
            
            <!-- Suhu -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500 flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-500 mr-4">
                    <i class="fa-solid fa-temperature-half text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Suhu Udara</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $latestData->temp }} °C</p>
                </div>
            </div>

            <!-- Kelembaban Udara -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <i class="fa-solid fa-droplet text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Kelembaban Udara</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $latestData->hum }} %</p>
                </div>
            </div>

            <!-- Kelembaban Tanah -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-amber-500 flex items-center">
                <div class="p-3 rounded-full bg-amber-100 text-amber-500 mr-4">
                    <i class="fa-solid fa-seedling text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Kelembaban Tanah</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $latestData->soil }} %</p>
                </div>
            </div>

            <!-- Amonia -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 {{ $latestData->ammonia > 30 ? 'border-red-500' : 'border-green-500' }} flex items-center">
                <div class="p-3 rounded-full {{ $latestData->ammonia > 30 ? 'bg-red-100 text-red-500' : 'bg-green-100 text-green-500' }} mr-4">
                    <i class="fa-solid fa-biohazard text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Kadar Amonia</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $latestData->ammonia }} ppm</p>
                </div>
            </div>

            <!-- Total Massa -->
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500 flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                    <i class="fa-solid fa-weight-hanging text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Massa Biopond</p>
                    @php
                        // Menjumlahkan array beban biopond
                        $biopondArray = is_array($latestData->biopond) ? $latestData->biopond : json_decode($latestData->biopond, true);
                        $totalBerat = array_sum($biopondArray);
                    @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalBerat / 1000, 2) }} kg</p>
                </div>
            </div>
        </div>

        <!-- Detail Per Rak Biopond -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-700">Massa Per Rak Biopond</h3>
                <span class="text-xs text-gray-400">Pembaruan Terakhir: {{ $latestData->created_at->diffForHumans() }}</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($biopondArray as $index => $berat)
                        <div class="bg-gray-50 rounded-lg p-4 text-center border border-gray-200">
                            <p class="text-xs text-gray-500 mb-1 font-bold">Rak {{ $index + 1 }}</p>
                            <p class="text-lg font-bold text-accent">{{ $berat }} g</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm" role="alert">
            <p class="font-bold">Data Kosong</p>
            <p>Belum ada data sensor yang masuk dari perangkat keras.</p>
        </div>
    @endif
@endsection