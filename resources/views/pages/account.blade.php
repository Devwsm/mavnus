{{--
    Halaman "Akun Saya" (overview, read-only). Edit data dipisah ke halaman
    tersendiri (pages.account-edit), diakses lewat pensil di avatar / header.
    Gaya "app shell": background abu-abu muda, card putih shadow lembut,
    quick-access status pesanan dengan angka real — mirip Shopee/Tokopedia.
--}}
@php($active = 'account')
@extends('template.account-layout')
@section('content')
    <section id="main-content"
        class="flex flex-col w-full bg-[#F5F6F8] gap-4 lg:gap-10 p-3 sm:p-6 lg:p-14 pt-24 md:pt-22 lg:pt-32 pb-24 lg:pb-14 min-h-screen">
        @include('components/errors/alerts')

        <div class="hidden lg:block">
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">Akun Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi & pesanan kamu di sini.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-8">
            {{-- Menu / sidebar --}}
            <div class="lg:col-span-1 order-1">
                <div class="lg:sticky lg:top-28">
                    @include('components.account.menu', ['active' => 'account'])
                </div>
            </div>

            {{-- Konten --}}
            <div class="lg:col-span-2 order-2 flex flex-col gap-4">
                {{-- Quick-access status pesanan --}}
                <div class="bg-white rounded-2xl shadow-sm shadow-black/5 p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-bold">Pesanan Saya</h2>
                        <a href="{{ route('account.orders') }}"
                            class="text-xs font-semibold text-gray-400 hover:text-black transition flex items-center gap-1">
                            Lihat Semua <i class="bi bi-chevron-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="grid grid-cols-4 gap-2 sm:gap-3">
                        <a href="{{ route('account.orders', ['status' => 'pending']) }}"
                            class="relative flex flex-col items-center gap-2 py-2 rounded-xl hover:bg-gray-50 active:scale-95 transition">
                            @if ($orderCounts['pending'] > 0)
                                <span
                                    class="absolute top-0 right-1/2 translate-x-4 -translate-y-1 min-w-4.5 h-4.5 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">
                                    {{ $orderCounts['pending'] }}
                                </span>
                            @endif
                            <span
                                class="w-11 h-11 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-lg">
                                <i class="bi bi-hourglass-split"></i>
                            </span>
                            <span
                                class="text-[11px] sm:text-xs font-medium text-gray-600 text-center leading-tight">Menunggu</span>
                        </a>
                        <a href="{{ route('account.orders', ['status' => 'processing']) }}"
                            class="relative flex flex-col items-center gap-2 py-2 rounded-xl hover:bg-gray-50 active:scale-95 transition">
                            @if ($orderCounts['processing'] > 0)
                                <span
                                    class="absolute top-0 right-1/2 translate-x-4 -translate-y-1 min-w-4.5 h-4.5 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">
                                    {{ $orderCounts['processing'] }}
                                </span>
                            @endif
                            <span
                                class="w-11 h-11 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-lg">
                                <i class="bi bi-box-seam"></i>
                            </span>
                            <span
                                class="text-[11px] sm:text-xs font-medium text-gray-600 text-center leading-tight">Diproses</span>
                        </a>
                        <a href="{{ route('account.orders', ['status' => 'shipped']) }}"
                            class="relative flex flex-col items-center gap-2 py-2 rounded-xl hover:bg-gray-50 active:scale-95 transition">
                            @if ($orderCounts['shipped'] > 0)
                                <span
                                    class="absolute top-0 right-1/2 translate-x-4 -translate-y-1 min-w-4.5 h-4.5 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">
                                    {{ $orderCounts['shipped'] }}
                                </span>
                            @endif
                            <span
                                class="w-11 h-11 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg">
                                <i class="bi bi-truck"></i>
                            </span>
                            <span
                                class="text-[11px] sm:text-xs font-medium text-gray-600 text-center leading-tight">Dikirim</span>
                        </a>
                        <a href="{{ route('account.orders', ['status' => 'completed']) }}"
                            class="relative flex flex-col items-center gap-2 py-2 rounded-xl hover:bg-gray-50 active:scale-95 transition">
                            <span
                                class="w-11 h-11 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-lg">
                                <i class="bi bi-check-circle"></i>
                            </span>
                            <span
                                class="text-[11px] sm:text-xs font-medium text-gray-600 text-center leading-tight">Selesai</span>
                        </a>
                    </div>
                </div>

                {{-- Informasi Akun --}}
                <div class="bg-white rounded-2xl shadow-sm shadow-black/5 overflow-hidden">
                    <div class="flex items-center justify-between px-4 sm:px-5 py-4">
                        <h2 class="text-sm font-bold">Informasi Akun</h2>
                        <a href="{{ route('account.edit') }}" aria-label="Edit Profil"
                            class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-black flex items-center justify-center shrink-0 active:scale-90 transition">
                            <i class="bi bi-pencil-fill text-sm"></i>
                        </a>
                    </div>

                    {{-- List info read-only --}}
                    <div class="flex flex-col divide-y divide-gray-100 px-4 sm:px-5">
                        <div class="flex items-center gap-4 py-3.5">
                            <span
                                class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-gray-400 uppercase tracking-wide">Nama</p>
                                <p class="text-sm font-semibold truncate">{{ $user->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 py-3.5">
                            <span
                                class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-gray-400 uppercase tracking-wide">Email</p>
                                <p class="text-sm font-semibold truncate">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 py-3.5">
                            <span
                                class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-gray-400 uppercase tracking-wide">Nomor HP</p>
                                <p class="text-sm font-semibold truncate">{{ $user->phone ?: '—' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 py-3.5">
                            <span
                                class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] text-gray-400 uppercase tracking-wide">Alamat</p>
                                <p class="text-sm font-semibold wrap-break-word">{{ $user->address ?: '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 sm:px-5 pb-4 pt-1">
                        <a href="{{ route('account.edit') }}"
                            class="flex items-center justify-center gap-2 w-full bg-black hover:bg-black/85 text-white text-sm font-bold py-3 rounded-xl transition active:scale-[0.99]">
                            <i class="bi bi-pencil"></i> Ubah Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
