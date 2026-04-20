@extends('layouts.app')

@section('title', 'Intervensi Aktuator')

@section('content')
    <!-- Notifikasi Sukses -->
    <div id="alertSuccess" class="hidden mb-4 sm:mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm text-sm sm:text-base" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline" id="alertMsg">Status aktuator telah diperbarui.</span>
    </div>

    <!-- PANEL SWITCH MODE OPERASIONAL -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6 mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <i class="fa-solid fa-microchip text-primary"></i> Mode Operasional Sistem
            </h3>
            <p class="text-sm text-gray-500 mt-1">Saat mode otomatis aktif, ESP32 akan menggunakan Fuzzy Logic dan tombol manual akan dikunci.</p>
        </div>
        
        <!-- Toggle Switch UI -->
        <label class="relative inline-flex items-center cursor-pointer select-none">
            <input type="checkbox" id="modeSwitch" class="sr-only peer" {{ $control->is_manual ? 'checked' : '' }} onchange="toggleMode()">
            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-accent"></div>
            <span class="ml-3 text-sm sm:text-base font-black tracking-wider w-24 {{ $control->is_manual ? 'text-accent' : 'text-gray-400' }}" id="modeLabel">
                {{ $control->is_manual ? 'MANUAL' : 'OTOMATIS' }}
            </span>
        </label>
    </div>

    <!-- WRAPPER UNTUK MENGUNCI TOMBOL JIKA MODE OTOMATIS -->
    <!-- Kelas pointer-events-none & opacity-60 akan aktif/hilang berdasarkan status mode -->
    <div id="manualControlContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-8 transition-all duration-300 {{ $control->is_manual ? '' : 'pointer-events-none opacity-60 grayscale-[30%]' }}">
        
        <!-- ========================================== -->
        <!-- PANEL KONTROL KIPAS -->
        <!-- ========================================== -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-bold text-gray-700"><i class="fa-solid fa-fan text-blue-500 mr-2"></i> Kontrol Kipas (Exhaust)</h3>
            </div>
            
            <div class="p-4 sm:p-6">
                <!-- BANNER INFO: Suhu & Udara -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 bg-blue-50 border border-blue-100 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6">
                    <div class="text-blue-800 text-xs sm:text-sm flex items-center gap-2">
                        <i class="fa-solid fa-temperature-half text-base sm:text-lg w-4"></i> Suhu Terkini: 
                        <strong class="text-sm sm:text-base">{{ $latestData ? $latestData->temp . ' °C' : '--' }}</strong>
                    </div>
                    <div class="hidden sm:block text-blue-300">|</div>
                    <div class="text-blue-800 text-xs sm:text-sm flex items-center gap-2">
                        <i class="fa-solid fa-cloud text-base sm:text-lg w-4"></i> Udara Terkini: 
                        <strong class="text-sm sm:text-base">{{ $latestData ? $latestData->hum . ' %' : '--' }}</strong>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6">Atur tingkat putaran 6 kipas secara bersamaan.</p>
                
                @php
                    $currentLevel = $control ? round($control->fan / 20) : 0;
                @endphp
                
                <!-- TOMBOL KONTROL AC MOBIL -->
                <div class="flex items-center justify-between sm:justify-center gap-1 sm:gap-2 mb-3 sm:mb-4 bg-gray-100 p-2 sm:p-3 rounded-2xl w-full">
                    <button id="fanBtn_0" onclick="setFanLevel(0)" class="w-11 h-11 sm:w-14 sm:h-14 text-xs sm:text-base rounded-full font-bold transition-all flex items-center justify-center {{ $currentLevel == 0 ? 'bg-red-500 text-white shadow-md' : 'bg-white text-gray-500 hover:bg-gray-200' }}">OFF</button>
                    <div class="hidden sm:block w-2 h-1 bg-gray-300 rounded"></div>
                    <button id="fanBtn_1" onclick="setFanLevel(1)" class="w-9 h-9 sm:w-12 sm:h-12 text-sm sm:text-base rounded-full font-bold transition-all flex items-center justify-center {{ $currentLevel == 1 ? 'bg-blue-500 text-white shadow-md' : 'bg-white text-gray-500 hover:bg-gray-200' }}">1</button>
                    <button id="fanBtn_2" onclick="setFanLevel(2)" class="w-9 h-9 sm:w-12 sm:h-12 text-sm sm:text-base rounded-full font-bold transition-all flex items-center justify-center {{ $currentLevel == 2 ? 'bg-blue-500 text-white shadow-md' : 'bg-white text-gray-500 hover:bg-gray-200' }}">2</button>
                    <button id="fanBtn_3" onclick="setFanLevel(3)" class="w-9 h-9 sm:w-12 sm:h-12 text-sm sm:text-base rounded-full font-bold transition-all flex items-center justify-center {{ $currentLevel == 3 ? 'bg-blue-500 text-white shadow-md' : 'bg-white text-gray-500 hover:bg-gray-200' }}">3</button>
                    <button id="fanBtn_4" onclick="setFanLevel(4)" class="w-9 h-9 sm:w-12 sm:h-12 text-sm sm:text-base rounded-full font-bold transition-all flex items-center justify-center {{ $currentLevel == 4 ? 'bg-blue-500 text-white shadow-md' : 'bg-white text-gray-500 hover:bg-gray-200' }}">4</button>
                    <div class="hidden sm:block w-2 h-1 bg-gray-300 rounded"></div>
                    <button id="fanBtn_5" onclick="setFanLevel(5)" class="w-11 h-11 sm:w-14 sm:h-14 text-xs sm:text-base rounded-full font-bold transition-all flex items-center justify-center {{ $currentLevel == 5 ? 'bg-blue-700 text-white shadow-lg' : 'bg-white text-gray-500 hover:bg-gray-200' }}">MAX</button>
                </div>
                <div class="text-center text-[10px] sm:text-xs text-gray-400 font-medium tracking-widest uppercase">Intensitas Kipas</div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PANEL KONTROL MIST MAKER -->
        <!-- ========================================== -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-bold text-gray-700"><i class="fa-solid fa-spray-can-sparkles text-teal-500 mr-2"></i> Kontrol Mist Maker</h3>
            </div>
            
            <div class="p-4 sm:p-6">
                <p class="text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6">Nyalakan atau matikan mist maker berdasarkan kondisi kelembaban tanah pada masing-masing rak biopond.</p>
                
                <!-- GRID TOMBOL MIST -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                    @php
                        $mistArray = $control ? (is_array($control->mist) ? $control->mist : json_decode($control->mist, true)) : [0,0,0,0,0,0];
                        $soilData = ($latestData && $latestData->soil) ? (is_array($latestData->soil) ? $latestData->soil : json_decode($latestData->soil, true)) : ['--','--','--','--','--','--'];
                    @endphp
                    
                    @foreach($mistArray as $index => $state)
                        <button id="mistBtn_{{ $index }}" 
                                onclick="toggleMist({{ $index }}, {{ $state }})" 
                                class="py-3 sm:py-4 px-2 rounded-xl border-2 font-bold transition-all flex flex-col items-center justify-center gap-1 sm:gap-2
                                {{ $state == 1 ? 'bg-teal-50 border-teal-500 text-teal-700 shadow-sm' : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                            <i class="fa-solid {{ $state == 1 ? 'fa-droplet' : 'fa-droplet-slash' }} text-xl sm:text-2xl"></i>
                            
                            <div class="flex flex-col items-center">
                                <span class="text-sm sm:text-base">Rak {{ $index + 1 }}</span>
                                <span class="text-[10px] sm:text-xs text-gray-500 font-normal mt-0.5"><i class="fa-solid fa-seedling text-amber-500"></i> Tanah: {{ $soilData[$index] ?? '--' }}{{ isset($soilData[$index]) && $soilData[$index] !== '--' ? '%' : '' }}</span>
                            </div>

                            <span class="text-[10px] sm:text-xs uppercase px-2 py-0.5 sm:py-1 rounded-full mt-1 {{ $state == 1 ? 'bg-teal-200 text-teal-800' : 'bg-gray-200 text-gray-600' }}" id="mistBadge_{{ $index }}">
                                {{ $state == 1 ? 'Menyala' : 'Mati' }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showAlert(message) {
        const alertBox = document.getElementById('alertSuccess');
        document.getElementById('alertMsg').innerText = message;
        alertBox.classList.remove('hidden');
        setTimeout(() => { alertBox.classList.add('hidden'); }, 3000);
    }

    // Fungsi Baru: Mengganti Mode Otomatis/Manual
    function toggleMode() {
        const isManual = document.getElementById('modeSwitch').checked ? 1 : 0;
        const container = document.getElementById('manualControlContainer');
        const label = document.getElementById('modeLabel');

        // Minta web untuk disabled sementara waktu saat loading
        document.getElementById('modeSwitch').disabled = true;

        fetch('/web-control', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ is_manual: isManual })
        }).then(response => {
            if(response.ok) {
                if(isManual) {
                    // MODE MANUAL AKTIF -> Lepas kunci CSS
                    container.classList.remove('pointer-events-none', 'opacity-60', 'grayscale-[30%]');
                    label.innerText = 'MANUAL';
                    label.classList.replace('text-gray-400', 'text-accent');
                    showAlert('Sistem dialihkan ke Mode MANUAL. Anda memiliki kendali penuh.');
                } else {
                    // MODE OTOMATIS AKTIF -> Kunci dengan CSS
                    container.classList.add('pointer-events-none', 'opacity-60', 'grayscale-[30%]');
                    label.innerText = 'OTOMATIS';
                    label.classList.replace('text-accent', 'text-gray-400');
                    showAlert('Sistem dialihkan ke Mode OTOMATIS (Fuzzy Logic).');
                }
            } else {
                // Kalau gagal kirim ke database, balikkan posisi switchnya
                document.getElementById('modeSwitch').checked = !isManual;
                alert('Gagal mengubah mode operasional!');
            }
            document.getElementById('modeSwitch').disabled = false;
        }).catch(err => {
            document.getElementById('modeSwitch').checked = !isManual;
            document.getElementById('modeSwitch').disabled = false;
            alert('Gagal tersambung ke server!');
        });
    }

    // Fungsi Set Kipas
    function setFanLevel(level) {
        const fanVal = level * 20;

        for(let i = 0; i <= 5; i++) {
            let btn = document.getElementById('fanBtn_' + i);
            btn.classList.remove('bg-red-500', 'bg-blue-500', 'bg-blue-700', 'text-white', 'shadow-md', 'shadow-lg');
            btn.classList.add('bg-white', 'text-gray-500', 'hover:bg-gray-200');

            if(i === level) {
                btn.classList.remove('bg-white', 'text-gray-500', 'hover:bg-gray-200');
                if(level === 0) btn.classList.add('bg-red-500', 'text-white', 'shadow-md');
                else if(level === 5) btn.classList.add('bg-blue-700', 'text-white', 'shadow-lg');
                else btn.classList.add('bg-blue-500', 'text-white', 'shadow-md');
            }
        }

        fetch('/web-control', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ fan: fanVal })
        }).then(response => {
            if(!response.ok) throw new Error('Error');
        }).catch(err => alert('Gagal mengirim perintah kipas!'));
    }

    // Update Mist Maker
    function toggleMist(index, currentState) {
        const newState = currentState === 1 ? 0 : 1;
        const btn = document.getElementById('mistBtn_' + index);
        const badge = document.getElementById('mistBadge_' + index);
        const icon = btn.querySelector('i');
        
        icon.className = 'fa-solid fa-spinner fa-spin text-xl sm:text-2xl';

        fetch('/web-control', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ mist_index: index, mist_value: newState })
        }).then(response => {
            if(response.ok) {
                if (newState === 1) {
                    btn.classList.remove('bg-white', 'border-gray-200', 'text-gray-400', 'hover:border-gray-300');
                    btn.classList.add('bg-teal-50', 'border-teal-500', 'text-teal-700', 'shadow-sm');
                    icon.className = 'fa-solid fa-droplet text-xl sm:text-2xl';
                    badge.className = 'text-[10px] sm:text-xs uppercase px-2 py-0.5 sm:py-1 rounded-full bg-teal-200 text-teal-800';
                    badge.innerText = 'Menyala';
                } else {
                    btn.classList.remove('bg-teal-50', 'border-teal-500', 'text-teal-700', 'shadow-sm');
                    btn.classList.add('bg-white', 'border-gray-200', 'text-gray-400', 'hover:border-gray-300');
                    icon.className = 'fa-solid fa-droplet-slash text-xl sm:text-2xl';
                    badge.className = 'text-[10px] sm:text-xs uppercase px-2 py-0.5 sm:py-1 rounded-full bg-gray-200 text-gray-600';
                    badge.innerText = 'Mati';
                }
                
                btn.setAttribute('onclick', `toggleMist(${index}, ${newState})`);
            } else {
                throw new Error('Error');
            }
        }).catch(err => {
            alert('Gagal mengirim perintah!');
            icon.className = currentState === 1 ? 'fa-solid fa-droplet text-xl sm:text-2xl' : 'fa-solid fa-droplet-slash text-xl sm:text-2xl';
        });
    }
</script>
@endpush