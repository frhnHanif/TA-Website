@extends('layouts.app')

@section('title', 'Statistik & Analitik Lanjutan')

@section('content')
    <!-- KPI Cards (Indikator Kinerja Utama) — 12 Bulan Terakhir -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        @php
            function formatMass($grams) {
                // Database menyimpan dalam GRAM, konversi ke KG dulu
                $kg = $grams / 1000;
                if ($kg >= 1000) {
                    return number_format($kg / 1000, 2, ',', '.') . ' <span class="text-lg font-medium">ton</span>';
                }
                return number_format($kg, 2, ',', '.') . ' <span class="text-lg font-medium">kg</span>';
            }
        @endphp

        {{-- 1. Total Akumulasi Panen --}}
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-[1.5rem] shadow-sm p-5 text-white flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-green-100 text-xs font-bold uppercase tracking-wider mb-1">Total Panen</p>
                <h3 class="text-2xl font-black">{!! formatMass($totalHarvest) !!}</h3>
            </div>
            <i class="fa-solid fa-box-open text-4xl opacity-30"></i>
        </div>

        {{-- 2. Total Sampah Organik Diolah (BARU - UI GreenMetric WS.3) --}}
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-[1.5rem] shadow-sm p-5 text-white flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-amber-100 text-xs font-bold uppercase tracking-wider mb-1">Sampah Diolah</p>
                <h3 class="text-2xl font-black">{!! formatMass($totalWasteInput) !!}</h3>
            </div>
            <i class="fa-solid fa-trash-can text-4xl opacity-30"></i>
        </div>

        {{-- 3. Total Residu / Kasgot (BARU - UI GreenMetric Inovasi Limbah) --}}
        <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-[1.5rem] shadow-sm p-5 text-white flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-teal-100 text-xs font-bold uppercase tracking-wider mb-1">Residu Kasgot</p>
                <h3 class="text-2xl font-black">{!! formatMass($totalResidue) !!}</h3>
            </div>
            <i class="fa-solid fa-seedling text-4xl opacity-30"></i>
        </div>

        {{-- 4. Rata-rata Waste Reduction (WRI) --}}
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-[1.5rem] shadow-sm p-5 text-white flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-blue-100 text-xs font-bold uppercase tracking-wider mb-1">Rata-rata WRI</p>
                <h3 class="text-2xl font-black">{{ number_format($avgWri, 1, ',', '.') }} <span class="text-lg font-medium">%/hari</span></h3>
            </div>
            <i class="fa-solid fa-recycle text-4xl opacity-30"></i>
        </div>

        {{-- 5. Efisiensi Biokonversi (ECI) --}}
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-[1.5rem] shadow-sm p-5 text-white flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-purple-100 text-xs font-bold uppercase tracking-wider mb-1">Rata-rata ECI</p>
                <h3 class="text-2xl font-black">{{ number_format($avgEci, 1, ',', '.') }} <span class="text-lg font-medium">%</span></h3>
            </div>
            <i class="fa-solid fa-bug text-4xl opacity-30"></i>
        </div>
    </div>

    <!-- Filter Toggle -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-gray-800">Visualisasi Tren Data</h2>
        
        <!-- PERBAIKAN: Menggunakan Grid 2 kolom di HP agar tidak overflow, lalu flex memanjang di sm/laptop -->
        <div class="bg-gray-200 p-1.5 rounded-xl grid grid-cols-2 sm:flex w-full md:w-auto shadow-inner gap-1.5">
            <button onclick="updateCharts('daily')" id="btn-daily" class="filter-btn px-3 sm:px-6 py-2 text-xs sm:text-sm font-medium rounded-lg text-gray-500 hover:text-gray-800 transition-all">Harian</button>
            <button onclick="updateCharts('weekly')" id="btn-weekly" class="filter-btn px-3 sm:px-6 py-2 text-xs sm:text-sm font-medium rounded-lg text-gray-500 hover:text-gray-800 transition-all">Mingguan</button>
            <button onclick="updateCharts('monthly')" id="btn-monthly" class="filter-btn px-3 sm:px-6 py-2 text-xs sm:text-sm font-bold rounded-lg bg-white shadow-sm text-primary transition-all">Bulanan</button>
            <button onclick="updateCharts('yearly')" id="btn-yearly" class="filter-btn px-3 sm:px-6 py-2 text-xs sm:text-sm font-medium rounded-lg text-gray-500 hover:text-gray-800 transition-all">Tahunan</button>
        </div>
    </div>

    <!-- Area Grafik -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Grafik Suhu & Kelembaban (Dual Axis) -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-6 relative">
            <div id="loader-TempHum" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 hidden flex justify-center items-center rounded-[1.5rem]"><i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i></div>
            <h3 class="text-lg font-bold text-gray-700 mb-2">Tren Suhu & Kelembaban Udara</h3>
            <p class="text-xs text-gray-400 mb-4" id="desc-TempHum">Pergerakan rata-rata per hari (30 hari terakhir)</p>
            <div id="chartTempHum" style="min-height:300px"></div>
        </div>

        <!-- Grafik Gas Amonia -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-6 relative">
            <div id="loader-Ammonia" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 hidden flex justify-center items-center rounded-[1.5rem]"><i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i></div>
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-bold text-gray-700">Fluktuasi Gas Amonia</h3>
                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded">Batas: 30 ppm</span>
            </div>
            <p class="text-xs text-gray-400 mb-4" id="desc-Ammonia">Pergerakan rata-rata per hari (30 hari terakhir)</p>
            <div id="chartAmmonia" style="min-height:300px"></div>
        </div>

        <!-- Grafik Tren Total Massa -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-6 relative">
            <div id="loader-TotalMass" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 hidden flex justify-center items-center rounded-[1.5rem]"><i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i></div>
            <h3 class="text-lg font-bold text-gray-700 mb-2">Tren Pertumbuhan Massa Total</h3>
            <p class="text-xs text-gray-400 mb-4" id="desc-TotalMass">Pergerakan rata-rata per hari (30 hari terakhir)</p>
            <div id="chartTotalMass" style="min-height:300px"></div>
        </div>

        <!-- Grafik Tren Pengolahan Sampah Organik (UI GreenMetric WS.3 / GD.2) -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-6 relative">
            <div id="loader-WasteTrend" class="absolute inset-0 bg-white/70 backdrop-blur-sm z-10 hidden flex justify-center items-center rounded-[1.5rem]"><i class="fa-solid fa-spinner fa-spin text-3xl text-primary"></i></div>
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-bold text-gray-700">Tren Pengolahan Sampah Organik</h3>
                <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-1 rounded">UI GreenMetric WS.3</span>
            </div>
            <p class="text-xs text-gray-400 mb-4" id="desc-WasteTrend">Total sampah organik yang diolah per bulan (1 tahun terakhir)</p>
            <div id="chartWasteTrend" style="min-height:300px"></div>
        </div>

    </div>

@endsection

@push('scripts')
{{-- ApexCharts: deferred agar tidak blocking render --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
<script>
    // Inisialisasi Objek Chart secara global
    let chartTempHum, chartAmmonia, chartTotalMass, chartWasteTrend;

    // Data Awal dari Controller saat halaman pertama dimuat
    let timeLabels = {!! json_encode($timestamps) !!};
    let tempData = {!! json_encode($tempData) !!};
    let humData = {!! json_encode($humData) !!};
    let ammoniaData = {!! json_encode($ammoniaData) !!};
    let totalMassData = {!! json_encode($totalMassData) !!};
    let wasteTrendLabels = {!! json_encode($wasteTrendLabels) !!};
    let wasteTrendData = {!! json_encode($wasteTrendData) !!};
    
    // Perbaikan: Menampilkan Label Sumbu X agar rentang waktu terlihat jelas
    const commonOptions = {
        chart: { toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'inherit' },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: { 
            categories: timeLabels, 
            labels: { 
                show: true, 
                rotate: -45, // Memiringkan teks agar tidak menumpuk
                style: { colors: '#9ca3af', fontSize: '11px', fontFamily: 'inherit' }
            }, 
            axisTicks: { show: true },
            tickAmount: 6 // Membatasi jumlah label yang tampil di bawah grafik
        },
        tooltip: {
            theme: 'light',
            x: { show: true }
        }
    };

    // Helper format satuan untuk grafik (data dalam GRAM dari DB)
    function formatWasteMass(grams) {
        const kg = grams / 1000;
        if (kg >= 1000) return (kg / 1000).toFixed(2) + ' ton';
        return kg.toFixed(2) + ' kg';
    }

    // Fungsi render semua grafik
    function renderAllCharts() {
        chartTempHum = new ApexCharts(document.querySelector("#chartTempHum"), {
            ...commonOptions,
            series: [
                { name: 'Suhu (&deg;C)', type: 'line', data: tempData },
                { name: 'Kelembaban (%)', type: 'line', data: humData }
            ],
            chart: { height: 300, type: 'line', toolbar: { show: false } },
            colors: ['#f97316', '#3b82f6'],
            yaxis: [
                { title: { text: 'Suhu (&deg;C)', style: { color: '#f97316', fontSize: '10px' } }, labels: { style: { colors: '#f97316' } } },
                { opposite: true, title: { text: 'Kelembaban (%)', style: { color: '#3b82f6', fontSize: '10px' } }, labels: { style: { colors: '#3b82f6' } } }
            ],
            legend: { position: 'top' }
        });
        chartTempHum.render();

        chartAmmonia = new ApexCharts(document.querySelector("#chartAmmonia"), {
            ...commonOptions,
            series: [{ name: 'Amonia (ppm)', data: ammoniaData }],
            chart: { height: 300, type: 'area', toolbar: { show: false } },
            colors: ['#ef4444'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] } },
            dataLabels: { enabled: false },
            annotations: { yaxis: [{ y: 30, borderColor: '#dc2626', label: { borderColor: '#dc2626', style: { color: '#fff', background: '#dc2626' }, text: 'Bahaya (>30 ppm)' } }] }
        });
        chartAmmonia.render();

        chartTotalMass = new ApexCharts(document.querySelector("#chartTotalMass"), {
            ...commonOptions,
            series: [{ name: 'Total Massa (kg)', data: totalMassData }],
            chart: { height: 300, type: 'area', toolbar: { show: false } },
            colors: ['#8b5cf6'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] } },
            dataLabels: { enabled: false },
            yaxis: { labels: { formatter: (value) => { return value ? value.toFixed(1) + " kg" : "0 kg" } } }
        });
        chartTotalMass.render();

        // Grafik Tren Pengolahan Sampah (BARU)
        chartWasteTrend = new ApexCharts(document.querySelector("#chartWasteTrend"), {
            series: [{ name: 'Sampah Diolah', data: wasteTrendData }],
            chart: { height: 300, type: 'bar', toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { borderRadius: 6, columnWidth: '55%', distributed: false } },
            colors: ['#f59e0b'],
            dataLabels: { 
                enabled: true,
                formatter: (val) => formatWasteMass(val),
                style: { fontSize: '11px', colors: ['#92400e'] }
            },
            xaxis: { 
                categories: wasteTrendLabels, 
                labels: { rotate: -45, style: { colors: '#9ca3af', fontSize: '11px' } } 
            },
            yaxis: { 
                labels: { formatter: (val) => formatWasteMass(val) },
                title: { text: 'Volume Sampah', style: { fontSize: '11px' } }
            },
            tooltip: { 
                theme: 'light',
                y: { formatter: (val) => formatWasteMass(val) }
            },
            noData: {
                text: 'Belum ada data siklus',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#9ca3af', fontSize: '14px' }
            }
        });
        chartWasteTrend.render();
    }

    // Inisialisasi grafik setelah DOM & ApexCharts siap, lalu fetch data bulanan
    document.addEventListener('DOMContentLoaded', () => {
        renderAllCharts();
        updateCharts('monthly');
    });

    // ==========================================
    // FUNGSI UPDATE GRAFIK DINAMIS (AJAX FETCH)
    // ==========================================
    function updateCharts(period) {
        // 1. Ubah warna tombol filter
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'shadow-sm', 'text-primary', 'font-bold');
            btn.classList.add('text-gray-500', 'hover:text-gray-800', 'font-medium');
        });
        const activeBtn = document.getElementById('btn-' + period);
        activeBtn.classList.remove('text-gray-500', 'hover:text-gray-800', 'font-medium');
        activeBtn.classList.add('bg-white', 'shadow-sm', 'text-primary', 'font-bold');

        // 2. Ubah Deskripsi Waktu di UI & Tampilkan Loader
        const textDesc = period === 'daily' ? 'Pergerakan rata-rata per jam (24 jam terakhir)' : 
                         (period === 'weekly' ? 'Pergerakan rata-rata per hari (7 hari terakhir)' : 
                         (period === 'monthly' ? 'Pergerakan rata-rata per hari (30 hari terakhir)' : 
                         'Pergerakan rata-rata per bulan (1 tahun terakhir)'));
        document.getElementById('desc-TempHum').innerText = textDesc;
        document.getElementById('desc-Ammonia').innerText = textDesc;
        document.getElementById('desc-TotalMass').innerText = textDesc;
        
        document.querySelectorAll('[id^="loader-"]').forEach(el => el.classList.remove('hidden'));

        // 3. Ambil data baru dari Server API
        fetch(`/api/statistik-data?period=${period}`)
            .then(res => res.json())
            .then(data => {
                // Update Grafik Suhu Hum
                chartTempHum.updateOptions({ xaxis: { categories: data.timestamps } });
                chartTempHum.updateSeries([
                    { name: 'Suhu (&deg;C)', data: data.tempData },
                    { name: 'Kelembaban (%)', data: data.humData }
                ]);

                // Update Grafik Amonia
                chartAmmonia.updateOptions({ xaxis: { categories: data.timestamps } });
                chartAmmonia.updateSeries([{ name: 'Amonia (ppm)', data: data.ammoniaData }]);

                // Update Grafik Total Massa
                chartTotalMass.updateOptions({ xaxis: { categories: data.timestamps } });
                chartTotalMass.updateSeries([{ name: 'Total Massa (kg)', data: data.totalMassData }]);

                // Sembunyikan Loader
                document.querySelectorAll('[id^="loader-"]').forEach(el => el.classList.add('hidden'));
            })
            .catch(err => {
                console.error("Gagal mengambil data statistik", err);
                document.querySelectorAll('[id^="loader-"]').forEach(el => el.classList.add('hidden'));
            });
    }
</script>
@endpush