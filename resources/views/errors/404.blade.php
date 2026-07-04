@extends('template.layout')
@section('content')
    <div class="min-h-screen flex flex-col uppercase items-center justify-center text-center px-4">
        <h1 class="text-8xl font-bold text-white">404</h1>
        <p class="text-2xl font-semibold text-white mt-4">Halaman tidak ditemukan</p>
        <p class="text-white mt-2">Halaman yang kamu cari tidak ada atau sudah dipindahkan.</p>
        <a href="{{ route('home') }}"
            class="mt-6 px-4 py-2 bg-white text-black rounded-lg font-semibold hover:bg-gray-100 transition">
            Kembali ke Beranda
        </a>
    </div>
@endsection
