@extends('template.dashboard.layout')
@section('content')
    @php
        $role = session('role');
        $canInvoice = in_array($role, ['owner', 'staff_pesanan']);
        $canProdukSql = in_array($role, ['owner', 'admin_produk']);
        $canOrderSql = $role === 'owner';
        $canFullBackup = $role === 'owner';
    @endphp
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
            {{-- Invoice & Laporan Order --}}
            @if ($canInvoice)
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                    <div>
                        <h2 class="text-lg font-bold uppercase tracking-wide">Invoice & Laporan Order</h2>
                        <p class="text-white/40 text-sm mt-1">
                            Pilih rentang tanggal dan status (boleh dikosongkan untuk semua pesanan), lalu preview atau
                            download. Invoice satu pesanan ada di halaman detail pesanan.
                        </p>
                    </div>
                    <form method="GET" class="flex flex-col gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label for="export-from"
                                    class="block text-white/40 text-[11px] uppercase tracking-wide mb-1.5">Dari
                                    tanggal</label>
                                <input type="date" id="export-from" name="from"
                                    class="w-full bg-black border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-[#B71C1C] scheme-dark">
                            </div>
                            <div>
                                <label for="export-to"
                                    class="block text-white/40 text-[11px] uppercase tracking-wide mb-1.5">Sampai
                                    tanggal</label>
                                <input type="date" id="export-to" name="to"
                                    class="w-full bg-black border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-[#B71C1C] scheme-dark">
                            </div>
                            <div>
                                <label for="export-status"
                                    class="block text-white/40 text-[11px] uppercase tracking-wide mb-1.5">Status</label>
                                <select id="export-status" name="status"
                                    class="w-full bg-black border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-[#B71C1C] scheme-dark">
                                    <option value="">Semua status</option>
                                    @foreach (\App\Models\Order::STATUSES as $statusOption)
                                        <option value="{{ $statusOption }}">{{ ucfirst($statusOption) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" formaction="{{ route('export.orders.preview') }}" formtarget="_blank"
                                class="flex-1 flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                                <i class="bi bi-eye"></i>
                                Preview Invoice
                            </button>
                            <button type="submit" formaction="{{ route('export.orders.pdf') }}"
                                class="flex-1 flex items-center justify-center gap-2 bg-[#B71C1C] hover:bg-[#891212] text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                                <i class="bi bi-file-earmark-pdf"></i>
                                Download PDF
                            </button>
                            <button type="submit" formaction="{{ route('export.orders') }}"
                                class="flex-1 flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                                <i class="bi bi-file-earmark-excel"></i>
                                Download Excel
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Export data per modul (.sql) --}}
            @if ($canProdukSql || $canOrderSql)
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                    <div>
                        <h2 class="text-lg font-bold uppercase tracking-wide">Export Data (.sql)</h2>
                        <p class="text-white/40 text-sm mt-1">
                            Dipakai setelah <code class="text-white/60">migrate:fresh --seed</code> di local.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        @if ($canProdukSql)
                            <a href="{{ route('export.products.sql') }}"
                                class="flex-1 flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                                <i class="bi bi-box-seam"></i>
                                Data Produk (.sql)
                            </a>
                        @endif
                        @if ($canOrderSql)
                            <a href="{{ route('export.orders.sql') }}"
                                class="flex-1 flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white text-sm font-semibold uppercase tracking-wide py-3 rounded-lg transition">
                                <i class="bi bi-receipt"></i>
                                Data Order (.sql)
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Backup lengkap untuk migrasi/deploy — paling sensitif, Owner-only --}}
            @if ($canFullBackup)
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
                </div>
            @endif

        </div>
    </div>
@endsection
