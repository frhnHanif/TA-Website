@extends('layouts.app')

@section('title', 'Manajemen Siklus Biopond')

@section('content')

    <!-- Flash Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative shadow-sm text-sm" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative shadow-sm text-sm" role="alert">
            <strong class="font-bold">Gagal!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($activeCycle)
        <!-- ========================================== -->
        <!-- BAGIAN 1A: STATUS SIKLUS AKTIF             -->
        <!-- ========================================== -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-6 sm:p-8 mb-6 relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-50 rounded-full blur-3xl opacity-60"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 relative z-10">
                
                <div class="w-full lg:w-1/2">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-green-100 text-green-700 font-black text-xs px-3 py-1 rounded-full uppercase tracking-wider animate-pulse flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span> Siklus Berjalan
                        </span>
                        <span class="text-gray-400 font-bold text-sm">{{ $activeCycle->batch_id }}</span>
                    </div>
                    <h2 class="text-3xl font-black text-gray-800 mb-1">Hari ke-{{ $activeCycle->days_elapsed }} <span class="text-lg text-gray-400 font-medium tracking-normal">dari target 21 Hari</span></h2>
                    <p class="text-sm text-gray-500">Siklus dimulai pada {{ $activeCycle->start_date->translatedFormat('d F Y') }}.</p>
                </div>

                <div class="w-full lg:w-auto flex flex-wrap gap-3">
                    <button onclick="openModal('modalPakan')" class="flex-1 lg:flex-none bg-white border-2 border-gray-200 hover:border-amber-400 text-gray-700 font-bold py-2.5 px-5 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus text-amber-500"></i> Catat Pakan
                    </button>
                    <button onclick="openModal('modalPanen')" class="flex-1 lg:flex-none bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 px-5 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-check-double"></i> Selesai Panen
                    </button>
                </div>
            </div>

            <div class="mt-8 relative z-10">
                <div class="flex justify-between text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">
                    <span>0 Hari</span>
                    @php $progress = min(100, ($activeCycle->days_elapsed / 21) * 100); @endphp
                    <span class="text-amber-600">Progres ({{ round($progress) }}%)</span>
                    <span>21 Hari</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden border border-gray-200/50">
                    <div class="bg-gradient-to-r from-amber-300 to-amber-500 h-full rounded-full transition-all duration-1000 relative" style="width: {{ $progress }}%">
                        <div class="absolute top-0 right-0 bottom-0 left-0 bg-gradient-to-r from-transparent via-white/30 to-transparent w-full animate-[shimmer_2s_infinite]"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 relative z-10 border-t border-gray-100 pt-6">
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Massa Bibit Awal</p>
                    <p class="text-xl font-black text-gray-800">{{ $activeCycle->initial_seed_mass }} <span class="text-sm font-medium text-gray-500">gram</span></p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Total Pakan Masuk</p>
                    <p class="text-xl font-black text-gray-800">{{ $activeCycle->total_waste_input }} <span class="text-sm font-medium text-gray-500">kg</span></p>
                </div>
                <div>
                    <p class="text-[10px] text-blue-500 font-bold uppercase tracking-wider mb-1">Estimasi Panen (15%)</p>
                    <p class="text-xl font-black text-blue-600">{{ number_format($activeCycle->total_waste_input * 0.15, 1) }} <span class="text-sm font-medium text-blue-400">kg</span></p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Suhu / Hum Rata-rata</p>
                    <p class="text-xl font-black text-gray-800">{{ $avgTemp }}° <span class="text-gray-300">|</span> {{ $avgHum }}%</p>
                </div>
            </div>
        </div>
    @else
        <!-- ========================================== -->
        <!-- BAGIAN 1B: EMPTY STATE (BELUM ADA SIKLUS)  -->
        <!-- ========================================== -->
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-8 sm:p-12 mb-6 text-center">
            <div class="w-24 h-24 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-4xl mx-auto mb-5 shadow-inner">
                <i class="fa-solid fa-leaf"></i>
            </div>
            <h2 class="text-2xl font-black text-gray-800 mb-2">Sistem Sedang Siaga (Standby)</h2>
            <p class="text-gray-500 max-w-md mx-auto mb-8">Belum ada siklus budidaya yang berjalan. Mulai siklus baru untuk melacak pemberian pakan, memantau pertumbuhan, dan mengestimasi hasil panen.</p>
            
            <button onclick="openModal('modalMulai')" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 mx-auto">
                <i class="fa-solid fa-play"></i> Mulai Siklus Baru
            </button>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- BAGIAN 2: RIWAYAT SIKLUS (SELESAI)         -->
    <!-- ========================================== -->
    <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-gray-400"></i> Riwayat Siklus (Log Batch)
                </h2>
                <p class="text-sm text-gray-500 mt-1">Data performa ECI dan WRI dari siklus yang telah selesai dipanen.</p>
            </div>
        </div>

        <div class="overflow-x-auto p-6">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="border-b-2 border-gray-100">
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider pl-2">ID Siklus</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Durasi</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Sampah Input</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Panen Prepupa</th>
                        <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Kasgot</th>
                        <th class="pb-3 text-xs font-black text-indigo-500 uppercase tracking-wider">WRI</th>
                        <th class="pb-3 text-xs font-black text-purple-500 uppercase tracking-wider text-right pr-2">ECI</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($finishedCycles as $fc)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 pl-2 font-bold text-gray-800">{{ $fc->batch_id }}</td>
                        <td class="py-4 text-gray-600">{{ $fc->days_elapsed }} Hari</td>
                        <td class="py-4 text-gray-600">{{ $fc->total_waste_input }} kg</td>
                        <td class="py-4 font-bold text-gray-800">{{ $fc->harvest_mass }} kg</td>
                        <td class="py-4 text-gray-600">{{ $fc->residue_mass }} kg</td>
                        <td class="py-4 font-bold text-indigo-600">{{ number_format($fc->wri_result, 1) }} %/hari</td>
                        <td class="py-4 text-right pr-2 font-black text-purple-600">{{ number_format($fc->eci_result, 1) }} %</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400 text-sm">Belum ada riwayat siklus yang diselesaikan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- KUMPULAN MODAL (POP-UP)                    -->
    <!-- ========================================== -->

    <!-- 1. Modal Mulai Siklus -->
    <div id="modalMulai" class="fixed inset-0 z-[100] bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center p-4 transition-opacity">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-xl overflow-hidden transform transition-all">
            <div class="bg-amber-500 px-6 py-4 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-play"></i> Mulai Siklus Baru</h3>
                <button onclick="closeModal('modalMulai')" class="text-amber-100 hover:text-white transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form action="{{ url('/cycle/start') }}" method="POST" class="p-6">
                @csrf
                <p class="text-sm text-gray-600 mb-5">Masukkan data inisiasi untuk rak biopond hari ini.</p>
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Massa Bibit Awal (5-DOL)</label>
                    <div class="relative">
                        <input type="number" step="0.1" name="bibit_awal" placeholder="Contoh: 50" class="bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-3 pr-16 outline-none transition-colors" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 font-bold text-sm">gram</div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Pakan Sampah Pertama</label>
                    <div class="relative">
                        <input type="number" step="0.1" name="pakan_awal" placeholder="Contoh: 5.5" class="bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-3 pr-12 outline-none transition-colors">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 font-bold text-sm">kg</div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2">*Biarkan kosong jika sensor load cell sudah mendeteksi otomatis.</p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('modalMulai')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-md">Simpan & Mulai</button>
                </div>
            </form>
        </div>
    </div>

    @if($activeCycle)
    <!-- 2. Modal Catat Pakan Manual -->
    <div id="modalPakan" class="fixed inset-0 z-[100] bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center p-4 transition-opacity">
        <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl overflow-hidden transform transition-all">
            <div class="px-6 pt-6 pb-4 flex justify-between items-center border-b border-gray-100">
                <h3 class="text-gray-800 font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-plus text-amber-500"></i> Catat Pakan</h3>
                <button onclick="closeModal('modalPakan')" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <!-- Form di dalam Modal Tambah Pakan -->
            <form action="{{ route('cycle.addWaste') }}" method="POST" class="p-6">                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    @for ($i = 1; $i <= 6; $i++)
                        <div>
                            <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Pakan Rak {{ $i }}</label>
                            <div class="relative">
                                <!-- Gunakan array pakan_rak[] untuk mengirim data -->
                                <input type="number" step="0.1" name="pakan_rak[{{ $i }}]" min="0" placeholder="0.0" class="bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 outline-none">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400 font-bold text-xs">kg</div>
                            </div>
                        </div>
                    @endfor
                </div>
                <p class="text-[10px] text-gray-400 mb-4">*Kosongkan rak yang tidak diberi tambahan pakan. Total akan diakumulasi otomatis.</p>
                
                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-xl text-sm px-5 py-3 text-center transition-colors">
                    Simpan Data Pakan
                </button>
            </form>
        </div>
    </div>

    <!-- 3. Modal Akhiri Siklus (Panen) -->
    <div id="modalPanen" class="fixed inset-0 z-[100] bg-gray-900/60 backdrop-blur-sm hidden items-center justify-center p-4 transition-opacity">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-xl overflow-hidden transform transition-all">
            <div class="bg-gray-800 px-6 py-4 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-flag-checkered text-green-400"></i> Selesaikan Siklus</h3>
                <button onclick="closeModal('modalPanen')" class="text-gray-400 hover:text-white transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form action="{{ url('/cycle/finish') }}" method="POST" class="p-6">
                @csrf
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                    <p class="text-sm text-blue-800 font-medium">Anda akan mengakhiri <strong>Siklus {{ $activeCycle->batch_id }}</strong>. Sistem akan menghitung indeks efisiensi (WRI & ECI) berdasarkan input akhir ini.</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Total Panen Prepupa Aktual</label>
                    <div class="relative">
                        <!-- Hapus atribut 'required', ubah placeholder agar user tahu ini otomatis -->
                        <input type="number" step="0.1" name="panen_aktual" placeholder="Otomatis ditarik dari sensor (Rak 7)" class="bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 pr-12 outline-none transition-colors">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 font-bold text-sm">kg</div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">*Kosongkan form ini untuk menggunakan data pembacaan sensor Load Cell Rak 7 saat ini.</p>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Total Sisa Kasgot (Pupuk)</label>
                    <div class="relative">
                        <!-- Hapus atribut 'required', ubah placeholder agar user tahu ini otomatis -->
                        <input type="number" step="0.1" name="kasgot_aktual" placeholder="Otomatis ditarik dari sensor (Rak 1-6)" class="bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3 pr-12 outline-none transition-colors">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 font-bold text-sm">kg</div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">*Kosongkan form ini untuk menggunakan total sisa bobot di Load Cell Rak 1-6.</p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('modalPanen')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-md">Simpan & Selesai</button>
                </div>
            </form>
        </div>
    </div>
    @endif

@endsection

@push('scripts')
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if(modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
@endpush