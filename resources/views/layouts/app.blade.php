<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EcoScale Dashboard')</title>
    
    <!-- FontAwesome & Google Fonts (Inter) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: '#f59e0b',
                        background: '#F8F9FA'
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #F8F9FA; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="text-gray-800 antialiased font-sans">

    <!-- ========================================== -->
    <!-- FLOATING TOP NAVIGATION                    -->
    <!-- ========================================== -->
    <nav class="fixed top-4 left-4 right-4 z-50 bg-white/90 backdrop-blur-md shadow-sm border border-gray-100 rounded-[2rem] px-4 sm:px-6 py-3 flex justify-between items-center transition-all max-w-screen-2xl mx-auto">
        
        <!-- LOGO BRAND -->
        <div class="flex items-center gap-3 relative z-20">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center text-white shadow-inner">
                <i class="fa-solid fa-leaf"></i>
            </div>
            <span class="font-extrabold text-xl text-gray-800 tracking-tight hidden sm:block">Eco<span class="text-amber-500">Scale</span></span>
        </div>

        <!-- MENU TENGAH (DESKTOP) -->
        <div class="hidden lg:flex items-center bg-gray-50 p-1 rounded-full border border-gray-200 shadow-inner relative z-20">
            <a href="/" class="{{ request()->is('/') ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-800 font-medium' }} px-5 py-2 rounded-full text-sm transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-border-all"></i> Dashboard
            </a>
            <a href="/statistik" class="{{ request()->is('statistik') ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-800 font-medium' }} px-5 py-2 rounded-full text-sm transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-chart-simple"></i> Statistik
            </a>
            <a href="/logbook" class="{{ request()->is('logbook') ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-800 font-medium' }} px-5 py-2 rounded-full text-sm transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-book-open"></i> Logbook
            </a>
            <a href="/control" class="{{ request()->is('control') ? 'bg-white shadow text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-800 font-medium' }} px-5 py-2 rounded-full text-sm transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-sliders"></i> Kontrol
            </a>
        </div>

        <!-- PROFILE & HAMBURGER -->
        <div class="flex items-center gap-3 relative z-20">
            <button class="w-10 h-10 hidden sm:flex items-center justify-center rounded-full border border-gray-200 text-gray-500 bg-white hover:bg-gray-50 transition-all hover:shadow-sm">
                <i class="fa-regular fa-bell"></i>
            </button>
            <div class="flex items-center gap-2 bg-white pr-4 pl-1 py-1 rounded-full border border-gray-200 cursor-pointer hover:shadow-sm transition-all">
                <img src="https://ui-avatars.com/api/?name=Admin+TPST&background=f59e0b&color=fff&bold=true" alt="Profile" class="w-8 h-8 rounded-full">
                <span class="text-sm font-bold text-gray-700 hidden md:block">Admin TPST</span>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button id="mobileMenuBtn" class="lg:hidden w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors focus:outline-none">
                <i id="menuIcon" class="fa-solid fa-bars"></i>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- DROPDOWN MOBILE MENU                       -->
        <!-- ========================================== -->
        <!-- Secara default disembunyikan pakai transform -translate-y-full dan opacity-0 -->
        <div id="mobileDropdown" class="absolute top-0 left-0 right-0 bg-white rounded-[2rem] shadow-xl border border-gray-100 pt-20 pb-6 px-6 transition-all duration-300 ease-in-out transform -translate-y-[120%] opacity-0 pointer-events-none z-10 lg:hidden">
            <div class="flex flex-col gap-2">
                <a href="/" class="{{ request()->is('/') ? 'bg-amber-50 text-amber-600 font-bold' : 'text-gray-600 hover:bg-gray-50' }} px-4 py-3 rounded-xl transition-colors flex items-center gap-3">
                    <i class="fa-solid fa-border-all w-5 text-center"></i> Dashboard
                </a>
                <a href="/statistik" class="{{ request()->is('statistik') ? 'bg-amber-50 text-amber-600 font-bold' : 'text-gray-600 hover:bg-gray-50' }} px-4 py-3 rounded-xl transition-colors flex items-center gap-3">
                    <i class="fa-solid fa-chart-simple w-5 text-center"></i> Statistik & Analitik
                </a>
                <a href="/logbook" class="{{ request()->is('logbook') ? 'bg-amber-50 text-amber-600 font-bold' : 'text-gray-600 hover:bg-gray-50' }} px-4 py-3 rounded-xl transition-colors flex items-center gap-3">
                    <i class="fa-solid fa-book-open w-5 text-center"></i> Logbook
                </a>
                <a href="/control" class="{{ request()->is('control') ? 'bg-amber-50 text-amber-600 font-bold' : 'text-gray-600 hover:bg-gray-50' }} px-4 py-3 rounded-xl transition-colors flex items-center gap-3">
                    <i class="fa-solid fa-sliders w-5 text-center"></i> Kontrol
                </a>
            </div>
        </div>
    </nav>

    <!-- Overlay Latar Belakang Gelap saat Mobile Menu Terbuka -->
    <div id="mobileOverlay" class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm z-40 transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden"></div>

    <!-- ========================================== -->
    <!-- MAIN CONTENT AREA                          -->
    <!-- ========================================== -->
    <main class="pt-28 pb-12 px-4 sm:px-6 max-w-screen-2xl mx-auto relative z-30">
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">@yield('title')</h1>
                <p class="text-sm text-gray-500 mt-1">Sistem Pemantauan Otomasi Biopond TPST Undip</p>
            </div>
        </div>

        @yield('content')
    </main>

    <!-- Script Hamburger Menu -->
    <script>
        const btn = document.getElementById('mobileMenuBtn');
        const icon = document.getElementById('menuIcon');
        const dropdown = document.getElementById('mobileDropdown');
        const overlay = document.getElementById('mobileOverlay');
        let isMenuOpen = false;

        function toggleMenu() {
            isMenuOpen = !isMenuOpen;
            if (isMenuOpen) {
                // Buka menu
                dropdown.classList.remove('-translate-y-[120%]', 'opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                icon.classList.replace('fa-bars', 'fa-xmark');
            } else {
                // Tutup menu
                dropdown.classList.add('-translate-y-[120%]', 'opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-0', 'pointer-events-none');
                icon.classList.replace('fa-xmark', 'fa-bars');
            }
        }

        // Klik tombol hamburger
        btn.addEventListener('click', toggleMenu);

        // Klik di luar menu (pada overlay gelap) akan menutup menu
        overlay.addEventListener('click', () => {
            if (isMenuOpen) toggleMenu();
        });
    </script>

    @stack('scripts')
</body>
</html>