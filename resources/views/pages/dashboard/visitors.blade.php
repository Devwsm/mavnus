@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col items-center justify-center p-8 text-center">
            <h1 class="text-3xl font-bold">Pengunjung</h1>
            <p class="text-white/50 text-sm mt-2 max-w-md">
                Pantau traffic dan aktivitas pengunjung website dari sini.
            </p>
        </div>

        <div class="flex flex-col w-full max-w-6xl gap-5 p-6 lg:p-14">

            {{-- Ringkasan cepat --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-1">
                    <span class="text-white text-2xl font-bold">{{ number_format($totalUniqueVisitors, 0, ',', '.') }}</span>
                    <span class="text-white/40 text-xs uppercase tracking-wide">Total Pengunjung</span>
                </div>
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-1">
                    <span class="text-white text-2xl font-bold">{{ number_format($totalVisits, 0, ',', '.') }}</span>
                    <span class="text-white/40 text-xs uppercase tracking-wide">Total Kunjungan</span>
                </div>
                <div class="bg-[#1C7B1C]/10 border border-[#1C7B1C]/20 rounded-2xl p-5 flex flex-col gap-1">
                    <span
                        class="text-[#3ECF3E] text-2xl font-bold">{{ number_format($uniqueVisitorsToday, 0, ',', '.') }}</span>
                    <span class="text-white/40 text-xs uppercase tracking-wide">Pengunjung Hari Ini</span>
                </div>
                <div class="bg-[#B71C1C]/10 border border-[#B71C1C]/20 rounded-2xl p-5 flex flex-col gap-1">
                    <span class="text-[#e05656] text-2xl font-bold">{{ number_format($visitsToday, 0, ',', '.') }}</span>
                    <span class="text-white/40 text-xs uppercase tracking-wide">Kunjungan Hari Ini</span>
                </div>
            </div>

            {{-- Grafik & Device --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Grafik 7 hari --}}
                <div class="lg:col-span-2 bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold uppercase tracking-wide">Kunjungan 7 Hari Terakhir</h2>
                        <span class="text-white/40 text-xs">{{ number_format($visitsThisWeek, 0, ',', '.') }}
                            kunjungan</span>
                    </div>

                    <div class="flex items-end justify-between gap-2 h-40 px-1">
                        @foreach ($dailyStats as $day)
                            @php
                                $heightPct = max(4, round(($day['total'] / $maxDaily) * 100));
                                $isToday = $day['date'] === now()->format('Y-m-d');
                            @endphp
                            <div class="flex flex-col items-center justify-end gap-2 flex-1 h-full group">
                                <span class="text-white/50 text-[11px] font-semibold group-hover:text-white transition">
                                    {{ $day['total'] }}
                                </span>
                                <div class="w-full rounded-t-md {{ $isToday ? 'bg-[#B71C1C]' : 'bg-white/15 group-hover:bg-white/30' }} transition-all"
                                    style="height: {{ $heightPct }}%"></div>
                                <span
                                    class="text-white/30 text-[10px] uppercase font-semibold {{ $isToday ? 'text-[#e05656]' : '' }}">
                                    {{ $day['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Breakdown perangkat --}}
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4">
                    <h2 class="text-sm font-bold uppercase tracking-wide">Perangkat</h2>

                    @php
                        $deviceLabels = [
                            'desktop' => ['Desktop', 'bi-display'],
                            'mobile' => ['Mobile', 'bi-phone'],
                            'tablet' => ['Tablet', 'bi-tablet'],
                            'bot' => ['Bot', 'bi-robot'],
                            'unknown' => ['Lainnya', 'bi-question-circle'],
                        ];
                    @endphp

                    <div class="flex flex-col gap-3">
                        @forelse ($deviceStats as $type => $count)
                            @php
                                $meta = $deviceLabels[$type] ?? ['Lainnya', 'bi-question-circle'];
                                $pct = round(($count / $totalDeviceVisits) * 100);
                            @endphp
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="flex items-center gap-1.5 text-white/70">
                                        <i class="bi {{ $meta[1] }}"></i> {{ $meta[0] }}
                                    </span>
                                    <span class="text-white/40">{{ $count }} · {{ $pct }}%</span>
                                </div>
                                <div class="w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#B71C1C] rounded-full" style="width: {{ $pct }}%">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-white/30 text-xs text-center py-6">Belum ada data pengunjung.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Halaman populer --}}
            <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4">
                <h2 class="text-sm font-bold uppercase tracking-wide">Halaman Populer</h2>

                <div class="flex flex-col gap-3">
                    @forelse ($topPages as $page)
                        @php $pct = round(($page->total / $maxPageVisit) * 100); @endphp
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center justify-between text-xs gap-3">
                                <div class="flex flex-col min-w-0">
                                    <span
                                        class="text-white/80 font-medium truncate">{{ \App\Models\Visit::friendlyLabel($page->url) }}</span>
                                    <span class="text-white/25 font-mono text-[10px] truncate">{{ $page->url }}</span>
                                </div>
                                <span class="text-white/40 shrink-0">{{ $page->total }} views</span>
                            </div>
                            <div class="w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-white/25 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-white/30 text-xs text-center py-6">Belum ada data halaman.</p>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat kunjungan --}}
            <div class="flex flex-col gap-4">
                <h2 class="text-xl font-bold uppercase tracking-wide">Riwayat Kunjungan</h2>

                <div class="flex flex-col gap-2">
                    @forelse ($recentVisits as $visit)
                        @php
                            $meta = $deviceLabels[$visit->device_type] ?? ['Lainnya', 'bi-question-circle'];
                        @endphp
                        <div
                            class="flex items-center justify-between bg-[#0D0D0D] border border-white/10 hover:border-white/20 rounded-xl px-5 py-4 transition gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <span
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white/5 text-white/60 shrink-0">
                                    <i class="bi {{ $meta[1] }}"></i>
                                </span>
                                <div class="flex flex-col min-w-0">
                                    <span
                                        class="text-sm font-semibold text-white truncate">{{ \App\Models\Visit::friendlyLabel($visit->url) }}</span>
                                    <span class="text-white/40 text-xs">
                                        {{ $visit->ip_address ?? 'IP tidak diketahui' }} ·
                                        {{ $visit->browser }} · {{ $meta[0] }}
                                    </span>
                                    <span class="text-white/20 font-mono text-[10px] truncate">{{ $visit->url }}</span>
                                </div>
                            </div>
                            <span class="text-white/40 text-xs shrink-0">{{ $visit->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="flex flex-col items-center gap-2 py-10 bg-[#0D0D0D] border border-white/10 rounded-xl">
                            <i class="bi bi-people text-white/15 text-3xl"></i>
                            <p class="text-white/30 text-sm">Belum ada kunjungan tercatat.</p>
                        </div>
                    @endforelse
                </div>

                @if ($recentVisits->hasPages())
                    <div class="mt-2">
                        {{ $recentVisits->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
