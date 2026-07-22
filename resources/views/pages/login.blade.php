@extends('template.layout')
@section('content')
    <main class="flex flex-col justify-center items-center bg-black w-full min-h-screen px-4 py-12">
        <div class="relative w-full max-w-sm bg-zinc-950 border border-zinc-800 rounded-2xl
                overflow-hidden">
            {{-- Header --}}
            <div class="px-8 pt-8 pb-6">
                <p class="text-[#B71C1C] text-[10px] font-semibold tracking-[0.3em] uppercase">Member Access</p>
                <h1 class="text-white text-4xl font-black uppercase tracking-tight leading-none mt-2">Mavnus</h1>
                <p class="text-zinc-500 text-sm mt-1">Crew & member Login</p>
            </div>
            {{-- Perforated seam --}}
            <div class="relative">
                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-black"></div>
                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-black"></div>
                <div class="border-t-2 border-dashed border-zinc-700 mx-6"></div>
            </div>
            {{-- Form --}}
            <div class="px-8 pt-6 pb-8">
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    @include('components/errors/alerts')
                    <div>
                        <label for="username"
                            class="block text-[11px] font-semibold tracking-widest uppercase text-zinc-500 mb-1.5">
                            Username
                        </label>
                        <input type="text" id="username" name="username"
                            class="w-full bg-black/60 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-zinc-600 focus:outline-none focus:border-[#B71C1C] focus:ring-1 focus:ring-[#B71C1C] transition"
                            placeholder="crew.username">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password"
                                class="block text-[11px] font-semibold tracking-widest uppercase text-zinc-500">
                                Password
                            </label>
                            <a href="#" class="text-[#B71C1C] hover:text-[#891212] text-xs transition">
                                Lupa password?</a>
                        </div>
                        <input type="password" id="password" name="password"
                            class="w-full bg-black/60 border border-zinc-800 rounded-lg px-4 py-2.5 text-white placeholder-zinc-600 focus:outline-none focus:border-[#B71C1C] focus:ring-1 focus:ring-[#B71C1C] transition"
                            placeholder="••••••••">
                    </div>
                    <button type="submit"
                        class="w-full bg-[#B71C1C] hover:bg-[#891212] text-black font-bold uppercase tracking-widest text-sm py-3 rounded-lg transition mt-2">
                        Masuk
                    </button>
                </form>

                {{-- Divider --}}
                <div class="flex items-center gap-3 my-6">
                    <div class="flex-1 border-t border-zinc-800"></div>
                    <span class="text-zinc-600 text-xs uppercase tracking-widest">atau</span>
                    <div class="flex-1 border-t border-zinc-800"></div>
                </div>
                {{-- Google login --}}
                <a href="#"
                    class="w-full flex items-center justify-center gap-3 border border-zinc-700 hover:border-zinc-500 bg-white/5 hover:bg-white/10 text-white font-medium text-sm py-3 rounded-lg transition">
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
                {{-- Barcode footer --}}
                <div class="mt-8">
                    <div class="h-6 w-full opacity-30"
                        style="background-image: repeating-linear-gradient(90deg, white 0 2px, transparent 2px 6px);"></div>
                    <p class="text-center text-[10px] tracking-[0.2em] text-zinc-700 uppercase mt-2">
                        Admit One • Mavnus Access
                    </p>
                </div>
            </div>
        </div>

        <p class="text-zinc-500 text-xs text-center mt-6">
            Belum punya akun? <a href="#" class="text-[#B71C1C] hover:underline">Hubungi staff</a>
        </p>
    </main>
@endsection
