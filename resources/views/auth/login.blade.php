@extends('layouts.app')

@section('title', 'Login Pengelola')

@section('content')
<div class="max-w-md mx-auto mt-10 mb-20 bg-white rounded-[1.5rem] shadow-sm border border-gray-100 p-8 sm:p-10 relative overflow-hidden">
    <!-- Efek Latar Belakang Blur -->
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-50 rounded-full blur-3xl opacity-60"></div>
    <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-orange-50 rounded-full blur-3xl opacity-60"></div>

    <div class="text-center mb-8 relative z-10">
        <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center text-white shadow-inner mx-auto mb-4 text-3xl">
            <i class="fa-solid fa-worm"></i>
        </div>
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">Login <span class="text-amber-500">Pengelola</span></h2>
        <p class="text-sm text-gray-500 mt-1">Sistem Informasi Manajemen Budidaya Maggot</p>
    </div>

    <!-- Session Status (Jika ada error/sukses dari Breeze) -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="relative z-10">
        @csrf

        <!-- Email Address -->
        <div class="mb-5">
            <label for="email" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                    <i class="fa-regular fa-envelope"></i>
                </div>
                <input id="email" class="bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full pl-11 p-3.5 outline-none transition-colors" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="admin@undip.ac.id" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500" />
        </div>

        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <input id="password" class="bg-gray-50 border border-gray-200 text-gray-800 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full pl-11 p-3.5 outline-none transition-colors" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-8">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-amber-500 shadow-sm focus:ring-amber-500 cursor-pointer w-4 h-4" name="remember">
                <span class="ml-2 text-sm text-gray-600 font-medium">Ingat Saya</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3.5 px-4 rounded-xl transition-all shadow-md hover:shadow-lg flex justify-center items-center gap-2">
            Masuk ke Sistem <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>
</div>
@endsection