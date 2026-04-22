<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SiMaggot Dashboard')</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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

    <nav class="fixed top-4 left-4 right-4 z-50 bg-white/90 backdrop-blur-md shadow-sm border border-gray-100 rounded-[2rem] px-4 sm:px-6 py-3 flex justify-between items-center max-w-screen-2xl mx-auto">
        
       <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center text-white shadow-inner">
                <i class="fa-solid fa-worm"></i>
            </div>
            <span class="font-extrabold text-xl text-gray-800 tracking-tight">Si<span class="text-amber-500">Maggot</span></span>
        </div>

        <div class="hidden lg:flex items-center bg-gray-50 p-1 rounded-full border border-gray-200 shadow-inner">
            <a href="/" class="{{ request()->is('/') ? 'bg-white shadow-sm text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-800 font-medium' }} px-5 py-2 rounded-full text-sm transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-border-all"></i> Dashboard
            </a>
            <a href="/statistik" class="{{ request()->is('statistik') ? 'bg-white shadow-sm text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-800 font-medium' }} px-5 py-2 rounded-full text-sm transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-chart-simple"></i> Statistik
            </a>
            <a href="/logbook" class="{{ request()->is('logbook') ? 'bg-white shadow-sm text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-800 font-medium' }} px-5 py-2 rounded-full text-sm transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-book-open"></i> Logbook
            </a>
            <a href="/control" class="{{ request()->is('control') ? 'bg-white shadow-sm text-gray-900 font-bold' : 'text-gray-500 hover:text-gray-800 font-medium' }} px-5 py-2 rounded-full text-sm transition-all duration-300 flex items-center gap-2">
                <i class="fa-solid fa-sliders"></i> Kontrol
            </a>
        </div>

        <div class="flex items-center gap-3">
            <button class="w-10 h-10 hidden sm:flex items-center justify-center rounded-full border border-gray-200 text-gray-500 bg-white hover:bg-gray-50 transition-all hover:shadow-sm">
                <i class="fa-regular fa-bell"></i>
            </button>
            <div class="flex items-center gap-2 bg-white pr-4 pl-1 py-1 rounded-full border border-gray-200 cursor-pointer hover:shadow-sm transition-all">
                <img src="https://ui-avatars.com/api/?name=Admin+TPST&background=f59e0b&color=fff&bold=true" alt="Profile" class="w-8 h-8 rounded-full">
                <span class="text-sm font-bold text-gray-700 hidden md:block">Admin TPST</span>
            </div>
        </div>
    </nav>

    <nav class="lg:hidden fixed bottom-4 left-4 right-4 z-50 bg-gray-50/95 backdrop-blur-xl p-1.5 rounded-[2rem] border border-gray-200 shadow-[0_8px_30px_rgb(0,0,0,0.12)] flex justify-between items-center">
        
        <a href="/" class="flex-1 flex flex-col items-center justify-center gap-1 py-2.5 rounded-full transition-all duration-300 {{ request()->is('/') ? 'bg-white shadow-sm text-amber-500 font-bold' : 'text-gray-400 hover:text-gray-600 font-medium' }}">
            <i class="fa-solid fa-border-all text-lg mb-0.5"></i>
            <span class="text-[10px] tracking-wide">Dashboard</span>
        </a>
        
        <a href="/statistik" class="flex-1 flex flex-col items-center justify-center gap-1 py-2.5 rounded-full transition-all duration-300 {{ request()->is('statistik') ? 'bg-white shadow-sm text-amber-500 font-bold' : 'text-gray-400 hover:text-gray-600 font-medium' }}">
            <i class="fa-solid fa-chart-simple text-lg mb-0.5"></i>
            <span class="text-[10px] tracking-wide">Statistik</span>
        </a>
        
        <a href="/logbook" class="flex-1 flex flex-col items-center justify-center gap-1 py-2.5 rounded-full transition-all duration-300 {{ request()->is('logbook') ? 'bg-white shadow-sm text-amber-500 font-bold' : 'text-gray-400 hover:text-gray-600 font-medium' }}">
            <i class="fa-solid fa-book-open text-lg mb-0.5"></i>
            <span class="text-[10px] tracking-wide">Logbook</span>
        </a>
        
        <a href="/control" class="flex-1 flex flex-col items-center justify-center gap-1 py-2.5 rounded-full transition-all duration-300 {{ request()->is('control') ? 'bg-white shadow-sm text-amber-500 font-bold' : 'text-gray-400 hover:text-gray-600 font-medium' }}">
            <i class="fa-solid fa-sliders text-lg mb-0.5"></i>
            <span class="text-[10px] tracking-wide">Kontrol</span>
        </a>

    </nav>

    <main class="pt-28 pb-28 lg:pb-12 px-4 sm:px-6 max-w-screen-2xl mx-auto relative z-30">
        <div class="mb-6 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-black text-gray-800 tracking-tight">@yield('title')</h1>
                <p class="text-sm text-gray-500 mt-1 hidden sm:block">Sistem Informasi Manajemen Budidaya Maggot TPST Undip</p>
            </div>
        </div>

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>