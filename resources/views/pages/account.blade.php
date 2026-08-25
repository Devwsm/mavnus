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

                    <form action="{{ route('account.update') }}" method="POST" class="flex flex-col gap-4">
                        @csrf
                        @include('components/errors/alerts')

                        <div>
                            <label for="name" class="block text-sm font-semibold mb-1.5">Nama</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold mb-1.5">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold mb-1.5">Nomor HP</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                                placeholder="08xxxxxxxxxx">
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-semibold mb-1.5">Alamat</label>
                            <textarea id="address" name="address" rows="3"
                                class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black resize-none"
                                placeholder="Alamat lengkap buat pengiriman">{{ old('address', $user->address) }}</textarea>
                        </div>

                        <div class="pt-4 mt-2 border-t border-black/10">
                            <label for="current_password" class="block text-sm font-semibold mb-1.5">Password Saat
                                Ini</label>
                            <input type="password" id="current_password" name="current_password"
                                class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                                placeholder="Konfirmasi buat simpan perubahan">
                            <p class="text-xs text-gray-400 mt-1.5">Demi keamanan, masukin password kamu tiap mau nyimpen
                                perubahan.</p>
                        </div>

                        <button type="submit"
                            class="bg-black hover:bg-black/80 text-white uppercase font-bold tracking-widest text-sm py-3.5 rounded-lg transition mt-2">
                            Simpan Perubahan
                        </button>
                    </form>

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
