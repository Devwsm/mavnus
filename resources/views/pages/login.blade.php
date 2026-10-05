{{-- Halaman login CUSTOMER, tema disamain sama halaman publik lain (checkout, dll) --}}
@extends('template.bare-layout')
@section('title', 'Masuk - Mavnus')
@section('content')
    <section id="main-content" class="flex flex-col items-center justify-center w-full bg-white gap-10 p-6 min-h-screen">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">Masuk</h1>
                <p class="text-sm text-gray-500 mt-2">Masuk ke akun Mavnus kamu</p>
            </div>

            <div class="border border-black/10 rounded-xl p-6">
                <form action="{{ route('login.proses') }}" method="POST" class="flex flex-col gap-4">
                    @csrf
                    @include('components/errors/alerts')

                    <div>
                        <label for="email" class="block text-sm font-semibold mb-1.5">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="nama@email.com">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-semibold">Password</label>
                            <a href="#" class="text-xs text-gray-500 hover:text-black transition">Lupa password?</a>
                        </div>
                        <input type="password" id="password" name="password"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="••••••••">
                    </div>

                    <button type="submit"
                        class="bg-black hover:bg-black/80 text-white uppercase font-bold tracking-widest text-sm py-3.5 rounded-lg transition mt-2">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="text-gray-500 text-sm text-center mt-6">
                Belum punya akun? <a href="{{ route('register') }}"
                    class="text-black font-semibold underline underline-offset-4 decoration-black/30 hover:decoration-black transition">Daftar
                    di sini</a>
            </p>
        </div>
    </section>
@endsection
