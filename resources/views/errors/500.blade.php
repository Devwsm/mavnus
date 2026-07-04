@extends('template.layout')
@section('content')
    <div class="min-h-screen flex flex-col uppercase items-center justify-center text-center px-4">
        <h1 class="text-8xl font-bold text-white">500</h1>
        <p class="text-2xl font-semibold text-white mt-4">Internal Server Error</p>
        <p class="text-white mt-2">Terjadi kesalahan pada server. Silakan coba lagi nanti.</p>
        <a href="{{ route('home') }}"
            class="mt-6 px-4 py-2 bg-white text-black rounded-lg font-semibold hover:bg-gray-100 transition">
            Kembali ke Beranda
        </a>
    </div>
@endsection
