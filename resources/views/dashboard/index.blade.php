@extends('layouts.app')

@section('title', 'Ringkasan Kondisi EcoScale')

@section('content')
    @if($latestData)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
            
            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500 flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-500 mr-4">
                    <i class="fa-solid fa-temperature-half text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Suhu Udara</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $latestData->temp }} °C</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <i class="fa-solid fa-droplet text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Kelembaban Udara</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $latestData->hum }} %</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-amber-500 flex items-center">
                <div class="p-3 rounded-full bg-amber-100 text-amber-500 mr-4">
                    <i class="fa-solid fa-seedling text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Tanah (Avg)</p>
                    @php
                        $soilArray = is_array($latestData->soil) ? $latestData->soil : json_decode($latestData->soil, true) ?? [];
                        $avgSoil = count($soilArray) > 0 ? round(array_sum($soilArray) / count($soilArray), 1) : 0;
                    @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ $avgSoil }} %</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 {{ $latestData->ammonia > 30 ? 'border-red-500' : 'border-green-500' }} flex items-center">
                <div class="p-3 rounded-full {{ $latestData->ammonia > 30 ? 'bg-red-100 text-red-500' : 'bg-green-100 text-green-500' }} mr-4">
                    <i class="fa-solid fa-biohazard text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Amonia</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $latestData->ammonia }} ppm</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500 flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                    <i class="fa-solid fa-weight-hanging text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-medium">Massa Total</p>
                    @php
                        $biopondArray = is_array($latestData->biopond) ? $latestData->biopond : json_decode($latestData->biopond, true) ?? [];
                        $totalBerat = array_sum($biopondArray);
                    @endphp
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalBerat / 1000, 2) }} kg</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-700">Detail Kondisi Tiap Rak Biopond</h3>
                <span class="text-xs text-gray-400 font-medium">Terakhir: {{ $latestData->created_at->diffForHumans() }}</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($biopondArray as $index => $berat)
                        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm flex flex-col gap-3 hover:border-accent transition-colors">
                            <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                                <span class="text-xs font-black text-primary uppercase">Rak {{ $index + 1 }}</span>
                                <i class="fa-solid fa-box text-gray-300"></i>
                            </div>
                            
                            <div class="flex flex-col">
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">Massa Maggot</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-lg font-bold text-primary">{{ $berat }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold">gram</span>
                                </div>
                            </div>

                            <div class="flex flex-col">
                                <span class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">Kelembaban Tanah</span>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-seedling text-amber-500 text-xs"></i>
                                    <span class="text-sm font-bold text-gray-700">{{ $soilArray[$index] ?? '--' }} %</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1 mt-1">
                                    <div class="bg-amber-400 h-1 rounded-full" style="width: {{ $soilArray[$index] ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm" role="alert">
            <p class="font-bold">Data Kosong</p>
            <p>Belum ada telemetri yang masuk dari sistem IoT.</p>
        </div>
    @endif
@endsection