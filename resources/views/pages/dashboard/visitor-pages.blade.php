@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col items-center justify-center p-8 text-center">
            <h1 class="text-3xl font-bold">Semua Halaman</h1>
            <p class="text-white/50 text-sm mt-2 max-w-md">
                Daftar lengkap halaman beserta jumlah kunjungannya.
            </p>
        </div>

        <div class="flex flex-col w-full max-w-3xl gap-5 p-6 lg:p-14">
            <a href="{{ route('dashboard.visitors') }}"
                class="text-white/40 hover:text-white text-sm font-semibold uppercase tracking-wide transition self-start">
                ← Kembali ke Pengunjung
            </a>

            <div class="flex flex-col gap-2">
                @forelse ($pages as $page)
                    @php $pct = round(($page->total / $maxPageVisit) * 100); @endphp
                    <a href="{{ route('dashboard.visitors', ['page_url' => $page->url]) }}"
                        class="flex flex-col gap-2 bg-[#0D0D0D] border border-white/10 hover:border-white/20 rounded-xl px-5 py-4 transition">
                        <div class="flex items-center justify-between text-sm gap-3">
                            <div class="flex flex-col min-w-0">
                                <span
                                    class="text-white font-semibold truncate">{{ \App\Models\Visit::friendlyLabel($page->url) }}</span>
                                <span class="text-white/30 font-mono text-xs truncate">{{ $page->url }}</span>
                            </div>
                            <span class="text-white/50 text-sm font-bold shrink-0">{{ $page->total }} views</span>
                        </div>
                        <div class="w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                            <div class="h-full bg-[#B71C1C] rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center gap-2 py-10 bg-[#0D0D0D] border border-white/10 rounded-xl">
                        <i class="bi bi-file-earmark-bar-graph text-white/15 text-3xl"></i>
                        <p class="text-white/30 text-sm">Belum ada data halaman.</p>
                    </div>
                @endforelse
            </div>

            @if ($pages->hasPages())
                <div class="mt-2">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
