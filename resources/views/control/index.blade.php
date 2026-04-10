@extends('layouts.app')

@section('title', 'Intervensi Aktuator')

@section('content')
    <!-- Notifikasi Sukses -->
    <div id="alertSuccess" class="hidden mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline" id="alertMsg">Status aktuator telah diperbarui.</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Panel Kontrol Kipas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-700"><i class="fa-solid fa-fan text-blue-500 mr-2"></i> Kontrol Kipas (Exhaust)</h3>
            </div>
            <div class="p-6">
                
                <!-- BANNER INFO: Suhu & Kelembaban Udara -->
                <div class="flex flex-wrap items-center gap-4 bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                    <div class="text-blue-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-temperature-half text-lg"></i> Suhu Terkini: 
                        <strong class="text-base">{{ $latestData ? $latestData->temp . ' °C' : '--' }}</strong>
                    </div>
                    <div class="hidden sm:block text-blue-300">|</div>
                    <div class="text-blue-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-cloud text-lg"></i> Kelembaban Udara Terkini: 
                        <strong class="text-base">{{ $latestData ? $latestData->hum . ' %' : '--' }}</strong>
                    </div>
                </div>

                <p class="text-sm text-gray-500 mb-4">Atur kecepatan putaran 6 kipas secara bersamaan menggunakan slider di bawah ini.</p>
                
                <div class="flex items-center gap-4 mb-6">
                    <span class="text-gray-500"><i class="fa-solid fa-power-off"></i></span>
                    <input type="range" id="fanSlider" min="0" max="100" value="{{ $control->fan }}" 
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                           oninput="document.getElementById('fanValue').innerText = this.value + '%'">
                    <span class="text-gray-500"><i class="fa-solid fa-wind"></i></span>
                </div>
                
                <div class="flex justify-between items-center">
                    <div class="text-3xl font-bold text-blue-600" id="fanValue">{{ $control->fan }}%</div>
                    <button onclick="updateFan()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-satellite-dish"></i> Kirim Perintah
                    </button>
                </div>
            </div>
        </div>

        <!-- Panel Kontrol Mist Maker -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-700"><i class="fa-solid fa-spray-can-sparkles text-teal-500 mr-2"></i> Kontrol Mist Maker</h3>
            </div>
            <div class="p-6">
                
                <!-- BANNER INFO: Kelembaban Tanah -->
                <div class="flex items-center gap-3 bg-teal-50 border border-teal-100 rounded-lg p-4 mb-6 text-teal-800 text-sm">
                    <i class="fa-solid fa-seedling text-lg"></i> Kelembaban Tanah Terkini: 
                    <strong class="text-base">{{ $latestData ? $latestData->soil . ' %' : '--' }}</strong>
                </div>

                <p class="text-sm text-gray-500 mb-6">Nyalakan atau matikan mist maker pada masing-masing rak biopond.</p>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @php
                        // Memastikan $control->mist terbaca sebagai array
                        $mistArray = is_array($control->mist) ? $control->mist : json_decode($control->mist, true);
                    @endphp
                    
                    @foreach($mistArray as $index => $state)
                        <button id="mistBtn_{{ $index }}" 
                                onclick="toggleMist({{ $index }}, {{ $state }})" 
                                class="py-4 px-2 rounded-xl border-2 font-bold transition-all flex flex-col items-center justify-center gap-2
                                {{ $state == 1 ? 'bg-teal-50 border-teal-500 text-teal-700 shadow-sm' : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300' }}">
                            <i class="fa-solid {{ $state == 1 ? 'fa-droplet' : 'fa-droplet-slash' }} text-2xl"></i>
                            <span>Rak {{ $index + 1 }}</span>
                            <span class="text-xs uppercase px-2 py-1 rounded-full {{ $state == 1 ? 'bg-teal-200 text-teal-800' : 'bg-gray-200 text-gray-600' }}" id="mistBadge_{{ $index }}">
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

    // Update Kipas
    function updateFan() {
        const fanVal = document.getElementById('fanSlider').value;
        const btn = event.currentTarget;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';
        btn.disabled = true;

        fetch('/web-control', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ fan: fanVal })
        }).then(response => {
            if(response.ok) showAlert('Kecepatan kipas berhasil diatur ke ' + fanVal + '%');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }).catch(err => {
            alert('Gagal mengirim perintah!');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // Update Mist Maker
    function toggleMist(index, currentState) {
        const newState = currentState === 1 ? 0 : 1;
        const btn = document.getElementById('mistBtn_' + index);
        const badge = document.getElementById('mistBadge_' + index);
        const icon = btn.querySelector('i');
        
        icon.className = 'fa-solid fa-spinner fa-spin text-2xl';

        fetch('/web-control', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ mist_index: index, mist_value: newState })
        }).then(response => {
            if(response.ok) {
                if (newState === 1) {
                    btn.className = 'py-4 px-2 rounded-xl border-2 font-bold transition-all flex flex-col items-center justify-center gap-2 bg-teal-50 border-teal-500 text-teal-700 shadow-sm';
                    icon.className = 'fa-solid fa-droplet text-2xl';
                    badge.className = 'text-xs uppercase px-2 py-1 rounded-full bg-teal-200 text-teal-800';
                    badge.innerText = 'Menyala';
                } else {
                    btn.className = 'py-4 px-2 rounded-xl border-2 font-bold transition-all flex flex-col items-center justify-center gap-2 bg-white border-gray-200 text-gray-400 hover:border-gray-300';
                    icon.className = 'fa-solid fa-droplet-slash text-2xl';
                    badge.className = 'text-xs uppercase px-2 py-1 rounded-full bg-gray-200 text-gray-600';
                    badge.innerText = 'Mati';
                }
                
                btn.setAttribute('onclick', `toggleMist(${index}, ${newState})`);
                showAlert(`Status Mist Maker Rak ${index + 1} diperbarui.`);
            }
        });
    }
</script>
@endpush