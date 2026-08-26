{{-- Halaman daftar akun CUSTOMER, tema disamain sama halaman publik lain (checkout, dll) --}}
@extends('template.bare-layout')
@section('title', 'Daftar - Mavnus')
@section('content')
    <section id="main-content" class="flex flex-col items-center justify-center w-full bg-white gap-10 p-6 min-h-screen">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">Daftar</h1>
                <p class="text-sm text-gray-500 mt-2">Buat akun baru buat belanja di Mavnus</p>
            </div>

            <div class="border border-black/10 rounded-xl p-6">
                <form action="{{ route('register.proses') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    @include('components/errors/alerts')

                    <div>
                        <label for="name" class="block text-sm font-semibold mb-1.5">Nama</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="Nama lengkap">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold mb-1.5">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="nama@email.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold mb-1.5">Password</label>
                        <input type="password" id="password" name="password"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="Minimal 8 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold mb-1.5">Konfirmasi
                            Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="Ulangi password">
                    </div>

                    <button type="submit"
                        class="bg-black hover:bg-black/80 text-white uppercase font-bold tracking-widest text-sm py-3.5 rounded-lg transition mt-2">
                        Daftar
                    </button>
                </form>
            </div>

            <p class="text-gray-500 text-sm text-center mt-6">
                Udah punya akun? <a href="{{ route('login') }}"
                    class="text-black font-semibold underline underline-offset-4 decoration-black/30 hover:decoration-black transition">Masuk
                    di sini</a>
            </p>
        </div>
    </section>
@endsection
