{{-- Halaman login CUSTOMER, tema disamain sama halaman publik lain (checkout, dll) --}}
@extends('template.layout')
@section('content')
    <div class="relative flex">
        @include('components/navbar')
    </div>
    <section id="main-content"
        class="flex flex-col items-center w-full bg-white gap-10 p-6 lg:p-14 pt-28 md:pt-22 lg:pt-32 min-h-screen">
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

                <div class="flex items-center gap-3 my-6">
                    <div class="flex-1 border-t border-black/10"></div>
                    <span class="text-gray-400 text-xs uppercase tracking-widest">atau</span>
                    <div class="flex-1 border-t border-black/10"></div>
                </div>

                <a href="#"
                    class="w-full flex items-center justify-center gap-3 border border-black/10 hover:border-black/30 text-sm font-medium py-3 rounded-lg transition">
                    <svg width="18" height="18" viewBox="0 0 48 48">
                        <path fill="#FFC107"
                            d="M43.6 20.5H42V20H24v8h11.3c-1.6 4.6-6 8-11.3 8-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.6 6 29.6 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.4-.4-3.5z" />
                        <path fill="#FF3D00"
                            d="M6.3 14.7l6.6 4.8C14.6 16 18.9 13 24 13c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.6 6 29.6 4 24 4c-7.4 0-13.8 4.1-17.1 10.1z" />
                        <path fill="#4CAF50"
                            d="M24 44c5.5 0 10.4-1.9 14.3-5.1l-6.6-5.4c-2 1.4-4.7 2.4-7.7 2.4-5.3 0-9.8-3.4-11.3-8.1l-6.6 5.1C9.9 39.6 16.4 44 24 44z" />
                        <path fill="#1976D2"
                            d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.2 4.2-4.1 5.6l6.6 5.4C41.6 35.4 44 30.2 44 24c0-1.2-.1-2.4-.4-3.5z" />
                    </svg>
                    Masuk dengan Google
                </a>
            </div>

            <p class="text-gray-500 text-sm text-center mt-6">
                Belum punya akun? <a href="{{ route('register') }}"
                    class="text-black font-semibold underline underline-offset-4 decoration-black/30 hover:decoration-black transition">Daftar
                    di sini</a>
            </p>
        </div>
    </section>
    @include('components/footer')
@endsection
