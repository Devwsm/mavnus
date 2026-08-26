@extends('template.bare-layout')
@section('title', 'Terlalu Banyak Percobaan - Mavnus')
@section('content')
    <div class="min-h-screen bg-black flex flex-col uppercase items-center justify-center text-center px-4">
        <h1 class="text-8xl font-bold text-white">429</h1>
        <p class="text-2xl font-semibold text-white mt-4">Terlalu Banyak Percobaan</p>
        <p class="text-white normal-case mt-2 max-w-sm">
            Kamu udah coba beberapa kali dalam waktu singkat. Tunggu sebentar (sekitar 1 menit), lalu coba lagi.
        </p>
        <a href="{{ route('home') }}"
            class="mt-6 px-4 py-2 bg-white text-black rounded-lg font-semibold hover:bg-gray-100 transition">
            Kembali ke Beranda
        </a>
    </div>
@endsection
