<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Panel - SiMaggot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f9fafb; /* bg-gray-50 */ }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 sm:p-6 text-gray-800">

    @if(!session('admin_unlocked'))
        <div class="bg-white p-8 rounded-[1.5rem] shadow-sm border border-gray-100 w-full max-w-sm text-center relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-48 h-48 bg-amber-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

            <div class="relative z-10">
                <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-5 shrink-0">
                    <i class="fa-solid fa-microchip"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800 mb-1">Configuration Panel</h2>
                <p class="text-xs text-gray-500 font-medium mb-6">Masukkan PIN keamanan untuk mengakses pengaturan sistem SiMaggot.</p>
                
                @if(session('error'))
                    <div class="bg-red-100 border border-red-200 text-red-600 text-sm font-bold p-3 rounded-xl mb-4">{{ session('error') }}</div>
                @endif

                <form action="/configuration-panel/unlock" method="POST">
                    @csrf
                    <input type="password" name="pin" required autofocus class="w-full text-center text-2xl tracking-[0.5em] font-bold p-4 border border-gray-200 rounded-xl focus:border-amber-400 focus:ring-2 focus:ring-amber-50 mb-4 transition-all outline-none bg-gray-50 focus:bg-white" placeholder="••••••" maxlength="6">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3.5 rounded-xl transition-colors shadow-sm">Buka Kunci</button>
                </form>
                <div class="mt-6">
                    <a href="/" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors"><i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Halaman Utama</a>
                </div>
            </div>
        </div>

    @else
        <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-100 w-full max-w-5xl overflow-hidden flex flex-col max-h-[95vh] relative">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

            <div class="px-6 sm:px-8 py-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">System Configuration</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Kelola kredensial akses Pengelola TPST.</p>
                    </div>
                </div>
                <form action="/configuration-panel/lock" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-bold text-sm px-4 py-2.5 rounded-xl transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-lock"></i> Kunci & Keluar
                    </button>
                </form>
            </div>

            <div class="p-6 sm:p-8 overflow-y-auto relative z-10">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm font-bold shadow-sm border border-green-200">{{ session('success') }}</div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6 text-sm font-bold shadow-sm border border-red-200">
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-gray-50 p-5 sm:p-6 rounded-2xl border border-gray-100 mb-8">
                    <h3 class="text-xs font-bold text-gray-400 mb-4 uppercase tracking-wider"><i class="fa-solid fa-user-plus mr-1"></i> Buat Kredensial Baru</h3>
                    <form action="/configuration-panel/store" method="POST" class="flex flex-col md:flex-row gap-3">
                        @csrf
                        <input type="text" name="name" placeholder="Nama Lengkap" required class="rounded-xl border border-gray-200 w-full text-sm p-3 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-50 transition-all">
                        <input type="email" name="email" placeholder="Alamat Email" required class="rounded-xl border border-gray-200 w-full text-sm p-3 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-50 transition-all">
                        <input type="password" name="password" placeholder="Password (Min 8 Karakter)" required class="rounded-xl border border-gray-200 w-full text-sm p-3 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-50 transition-all">
                        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-3 rounded-xl font-bold text-sm transition-colors shrink-0 shadow-sm">Simpan</button>
                    </form>
                </div>

                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Kredensial Aktif</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="border-b-2 border-gray-100">
                                <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider pl-2">Nama Pengelola</th>
                                <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider">Email Akses</th>
                                <th class="pb-3 text-xs font-black text-gray-400 uppercase tracking-wider text-right pr-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($users as $user)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 pl-2 font-bold text-gray-800">{{ $user->name }}</td>
                                <td class="py-4 text-gray-600">{{ $user->email }}</td>
                                <td class="py-4 pr-2 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')" class="text-blue-500 hover:text-blue-700 font-bold bg-blue-50 px-3 py-1.5 rounded-lg transition-colors text-xs flex items-center gap-1">
                                            <i class="fa-solid fa-pen text-[10px]"></i> Edit
                                        </button>
                                        
                                        <form action="/configuration-panel/destroy/{{ $user->id }}" method="POST" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold bg-red-50 px-3 py-1.5 rounded-lg transition-colors text-xs flex items-center gap-1" onclick="return confirm('Cabut akses untuk {{ $user->name }}?')">
                                                <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if($users->isEmpty())
                            <tr><td colspan="3" class="py-8 text-center text-gray-400 text-sm">Belum ada kredensial yang didaftarkan.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="bg-red-50/50 p-5 sm:p-6 rounded-2xl border border-red-100 mt-8">
                    <h3 class="text-xs font-bold text-red-600 mb-4 uppercase tracking-wider"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Pengaturan Darurat</h3>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Paksa Buka Kunci Aktuator</h4>
                            <p class="text-xs text-gray-500 mt-1 max-w-2xl">Gunakan fitur ini hanya jika tombol kontrol di web membeku atau terjadi error pada sistem penguncian sesi. Tombol ini akan mereset paksa perangkat keras dan web kembali ke mode OTOMATIS.</p>
                        </div>
                        <form action="/configuration-panel/force-unlock" method="POST" class="shrink-0 w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors shadow-sm flex justify-center items-center gap-2" onclick="return confirm('Apakah Anda yakin ingin memaksa sistem ke mode OTOMATIS dan menghapus semua kunci akses?')">
                                <i class="fa-solid fa-unlock-keyhole"></i> Force Unlock
                            </button>
                        </form>
                    </div>
                </div>
                </div>
        </div>

        <div id="editModal" class="fixed inset-0 z-50 bg-gray-900/40 hidden items-center justify-center backdrop-blur-sm p-4 transition-all">
            <div class="bg-white rounded-[1.5rem] shadow-xl w-full max-w-md overflow-hidden border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-sm font-bold text-gray-800"><i class="fa-solid fa-pen-to-square text-amber-500 mr-2"></i>Edit Kredensial</h3>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                
                <form id="editForm" method="POST" class="p-6">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" id="editName" name="name" required class="w-full rounded-xl border border-gray-200 text-sm p-3 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-50 transition-all bg-gray-50 focus:bg-white">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Alamat Email</label>
                        <input type="email" id="editEmail" name="email" required class="w-full rounded-xl border border-gray-200 text-sm p-3 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-50 transition-all bg-gray-50 focus:bg-white">
                    </div>
                    
                    <div class="mb-8">
                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Password Baru</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak ada perubahan" class="w-full rounded-xl border border-gray-200 text-sm p-3 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-50 transition-all bg-gray-50 focus:bg-white">
                        <p class="text-[10px] text-gray-400 mt-1.5">*Abaikan kolom ini jika tidak ingin me-reset password.</p>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-xl transition-colors text-sm">Batal</button>
                        <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-xl transition-colors text-sm shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditModal(id, name, email) {
                document.getElementById('editName').value = name;
                document.getElementById('editEmail').value = email;
                document.getElementById('editForm').action = '/configuration-panel/update/' + id;
                const modal = document.getElementById('editModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeEditModal() {
                const modal = document.getElementById('editModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        </script>
    @endif

</body>
</html>