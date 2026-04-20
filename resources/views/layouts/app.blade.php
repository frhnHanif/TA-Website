<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token Wajib untuk AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Vertical Biopond</title>
    
    <!-- Tailwind CSS (CDN untuk Prototyping Cepat) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Konfigurasi Warna Tema Custom Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2d3748',
                        secondary: '#4a5568',
                        accent: '#38a169', // Hijau Biopond
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside class="w-64 bg-primary text-white flex flex-col shadow-lg">
            <div class="h-16 flex items-center justify-center border-b border-gray-700">
                <h1 class="text-xl font-bold tracking-wider uppercase flex items-center gap-2">
                    <i class="fa-solid fa-leaf text-accent"></i> TPST Biopond
                </h1>
            </div>
            
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="/" class="{{ request()->is('/') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fa-solid fa-chart-pie w-5"></i>
                    <span>Halaman Utama</span>
                </a>
                <a href="/statistik" class="{{ request()->is('statistik') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fa-solid fa-chart-line w-5"></i>
                    <span>Statistik & Analitik</span>
                </a>
                <a href="/logbook" class="{{ request()->is('logbook') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fa-solid fa-book w-5"></i>
                    <span>e-Logbook</span>
                </a>
                <a href="/control" class="{{ request()->is('control') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} flex items-center gap-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fa-solid fa-gamepad w-5"></i>
                    <span>Kendali Aktuator</span>
                </a>
            </nav>

            <!-- Nanti tombol Logout ditaruh sini -->
            <div class="p-4 border-t border-gray-700">
                <button class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-red-600 rounded-lg transition-colors text-sm">
                    <i class="fa-solid fa-lock"></i> Mode Publik
                </button>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <div class="flex-1 flex flex-col overflow-y-auto relative">
            <!-- Header Atas -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10">
                <h2 class="text-xl font-semibold text-gray-700">
                    @yield('title', 'Dashboard')
                </h2>
                <div class="flex items-center gap-4 text-gray-500">
                    <span class="text-sm"><i class="fa-solid fa-clock"></i> <span id="clock"></span></span>
                </div>
            </header>

            <!-- Isi Halaman (Disuntikkan dari view lain) -->
            <main class="p-8 flex-1">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Script Jam Digital Sederhana -->
    <script>
        setInterval(() => {
            document.getElementById('clock').innerText = new Date().toLocaleTimeString('id-ID');
        }, 1000);
    </script>
    
    @stack('scripts')
</body>
</html>