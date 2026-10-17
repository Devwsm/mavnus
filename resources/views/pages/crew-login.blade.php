@extends('template.bare-layout')
@section('content')
    <main id="main-content" class="flex flex-col justify-center items-center bg-black w-full min-h-screen px-4 py-12">
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
                        class="w-full bg-[#B71C1C] hover:bg-[#891212] text-white font-bold uppercase tracking-widest text-sm py-3 rounded-lg transition mt-2">
                        Masuk
                    </button>
                </form>

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
