@extends('layouts.app')

@section('title', 'Riwayat Telemetri')

@section('content')
    <!-- ========================================== -->
    <!-- PANEL FILTER LAPORAN                       -->
    <!-- ========================================== -->
    <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-6 mb-6">
        <div class="mb-5">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-filter text-teal-600"></i> Filter Laporan
            </h2>
            <p class="text-sm text-gray-500 mt-1">Memfilter laporan telemetri sesuai rentang waktu yang diinginkan</p>
        </div>

        <form action="{{ url('/logbook') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
            
            <!-- Tanggal Mulai -->
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Mulai:</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-2.5 outline-none transition-colors">
                </div>
            </div>

            <!-- Tanggal Akhir -->
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Akhir:</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-2.5 outline-none transition-colors">
                </div>
            </div>

            <!-- Pengurutan (Sebagai pengganti "Fakultas" di referensi) -->
            <div class="w-full md:w-1/4">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Urutan Data:</label>
                <select name="sort" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm rounded-xl focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 outline-none transition-colors cursor-pointer">
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Terbaru ke Terlama</option>
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama ke Terbaru</option>
                </select>
            </div>

            <!-- Tombol Submit -->
            <div class="w-full md:w-1/4">
                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 px-4 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Terapkan Filter
                </button>
            </div>
            
            <!-- Tombol Reset (Muncul kalau ada filter aktif) -->
            @if(request()->has('start_date') || request()->has('end_date'))
                <a href="/logbook" class="w-full md:w-auto bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold py-2.5 px-4 rounded-xl transition-colors shadow-sm flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- ========================================== -->
    <!-- PANEL HASIL LAPORAN                        -->
    <!-- ========================================== -->
    <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        
        <!-- Header Hasil & Tombol Export -->
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-blue-600"></i> Hasil Laporan
                </h2>
                <p class="text-sm text-gray-500 mt-1">Generate laporan EcoScale (Menampilkan {{ $sensors->total() }} data)</p>
            </div>
            
            <!-- Tombol Export Excel -->
            <!-- Kita tambahkan &export=excel ke URL saat ini agar data yang terdownload sesuai dengan filter -->
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-xl transition-colors shadow-sm flex items-center gap-2 text-sm">
                <i class="fa-solid fa-file-excel"></i> Export ke Excel
            </a>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto p-6">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-100">
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider pl-2">Tanggal</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Waktu</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Suhu</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Hum Udara</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Hum Tanah (Avg)</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Amonia</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider text-right pr-2">Total Massa Maggot</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($sensors as $log)
                        @php
                            $biopondArray = is_array($log->biopond) ? $log->biopond : json_decode($log->biopond, true) ?? [];
                            $totalBerat = array_sum($biopondArray) / 1000;
                            
                            $soilArray = is_array($log->soil) ? $log->soil : json_decode($log->soil, true) ?? [];
                            $avgSoil = count($soilArray) > 0 ? array_sum($soilArray) / count($soilArray) : 0;
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 pl-2 font-medium text-gray-800">{{ $log->created_at->format('d/m/Y') }}</td>
                            <td class="py-4 text-gray-600">{{ $log->created_at->format('H:i') }}</td>
                            <td class="py-4 text-gray-600">{{ $log->temp }} °C</td>
                            <td class="py-4 text-gray-600">{{ $log->hum }} %</td>
                            <td class="py-4 text-gray-600">{{ number_format($avgSoil, 1) }} %</td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg {{ $log->ammonia > 30 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $log->ammonia }} ppm
                                </span>
                            </td>
                            <td class="py-4 text-right pr-2 font-bold text-gray-700">
                                {{ number_format($totalBerat, 2) }} kg
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="text-gray-300 mb-3"><i class="fa-solid fa-folder-open text-4xl"></i></div>
                                <p class="text-gray-500 font-medium">Tidak ada data ditemukan pada rentang waktu tersebut.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (Halaman) -->
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-sm text-gray-500">Halaman {{ $sensors->currentPage() }} dari {{ $sensors->lastPage() }}</span>
            <div class="flex items-center gap-2">
                {{ $sensors->links('pagination::tailwind') }} <!-- Pastikan kamu menggunakan style tailwind di AppServiceProvider jika tampilan navigasinya berantakan -->
            </div>
        </div>
        
    </div>
@endsection