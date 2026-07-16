@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col items-center justify-center p-8 text-center">
            <h1 class="text-3xl font-bold">Import / Export</h1>
            <p class="text-white/50 text-sm mt-2 max-w-md">
                Backup data untuk migrasi, atau export laporan untuk dilihat tim.
            </p>
        </div>

        <div class="flex flex-col w-full max-w-4xl gap-2 px-6 lg:px-14">
            @include('components/errors/alerts')
        </div>

        <div class="flex flex-col w-full max-w-4xl gap-6 p-6 lg:p-14">
            {{-- Laporan Excel --}}
            <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                <div>
                    <h2 class="text-lg font-bold uppercase tracking-wide">Laporan Excel</h2>
                    <p class="text-white/40 text-sm mt-1">
                        Untuk dilihat/dibagikan sebagai laporan, bukan untuk migrasi data.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('export.products') }}"
                        class="flex-1 flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        Export Produk
                    </a>
                    <a href="{{ route('export.orders') }}"
                        class="flex-1 flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                        <i class="bi bi-file-earmark-spreadsheet"></i>
                        Export Order
                    </a>
                </div>
            </div>

            {{-- Backup untuk migrasi/deploy --}}
            <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                <div>
                    <h2 class="text-lg font-bold uppercase tracking-wide">Backup Database & Foto</h2>
                    <p class="text-white/40 text-sm mt-1">
                        Dipakai sebelum update besar / pindah project. Simpan dua file ini bersamaan.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('export.database') }}"
                        class="flex-1 flex items-center justify-center gap-2 bg-[#B71C1C] hover:bg-[#891212] text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                        <i class="bi bi-database-down"></i>
                        Database Lengkap (.sql)
                    </a>
                    <a href="{{ route('export.storage') }}"
                        class="flex-1 flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                        <i class="bi bi-images"></i>
                        Foto (.zip)
                    </a>
                </div>
                <div class="border-t border-white/10 pt-4 mt-1">
                    <p class="text-white/40 text-xs mb-3">
                        Atau export data spesifik saja (dipakai setelah <code class="text-white/60">migrate:fresh
                            --seed</code> di local):
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('export.products.sql') }}"
                            class="flex-1 flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                            <i class="bi bi-box-seam"></i>
                            Data Produk (.sql)
                        </a>
                        <a href="{{ route('export.orders.sql') }}"
                            class="flex-1 flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                            <i class="bi bi-receipt"></i>
                            Data Order (.sql)
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
