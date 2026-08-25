{{--
    Halaman "Akun Saya" customer, tema disamain sama halaman publik lain (checkout, dll).
    Riwayat order belum kesambung ke sini (order belum punya kolom user_id),
    jadi bagian itu masih placeholder dulu.
--}}
@extends('template.layout')
@section('content')
    <div class="relative flex">
        @include('components/navbar')
    </div>
    <section id="main-content" class="flex flex-col w-full bg-white gap-10 p-6 lg:p-14 pt-28 md:pt-22 lg:pt-32">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">Akun Saya</h1>
            <p class="text-sm text-gray-500 mt-2">Kelola informasi & pesanan kamu di sini.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Sidebar menu --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-28 flex flex-col gap-4">
                    <div class="flex items-center gap-4 border border-black/10 rounded-xl p-6">
                        <div
                            class="w-14 h-14 rounded-full bg-black text-white flex items-center justify-center text-xl font-black uppercase shrink-0">
                            {{ Str::substr($user->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                    </div>

                    <nav class="flex flex-col border border-black/10 rounded-xl overflow-hidden">
                        <a href="{{ route('account') }}"
                            class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold uppercase tracking-wide bg-black text-white">
                            <i class="bi bi-person-fill"></i> Akun Saya
                        </a>
                        <a href="{{ route('account.orders') }}"
                            class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold uppercase tracking-wide border-t border-black/10 hover:bg-gray-50 transition">
                            <i class="bi bi-bag-fill"></i> Riwayat Pesanan
                        </a>
                        <a href="{{ route('logout') }}"
                            class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold uppercase tracking-wide border-t border-black/10 hover:bg-gray-50 transition text-gray-500">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Konten --}}
            <div class="lg:col-span-2">
                <div class="border border-black/10 rounded-xl p-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50 mb-5">Informasi Akun</h2>

                    <div class="flex flex-col gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Nama</p>
                            <p class="text-sm">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Email</p>
                            <p class="text-sm">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Member Sejak</p>
                            <p class="text-sm">{{ $user->created_at->translatedFormat('F Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-black/10">
                        <p class="text-sm text-gray-500">
                            <i class="bi bi-info-circle mr-1.5"></i>
                            Mau lihat pesanan kamu? Buka <a href="{{ route('account.orders') }}"
                                class="font-semibold underline underline-offset-4 decoration-black/30 hover:decoration-black transition">Riwayat
                                Pesanan</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('components/footer')
@endsection
