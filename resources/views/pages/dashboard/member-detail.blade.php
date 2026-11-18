@extends('template.dashboard.layout')
@section('content')
    @php
        $rupiah = fn($amount) => 'Rp' . number_format($amount, 0, ',', '.');
        $profile = [
            ['Email', $member->email],
            ['Nomor HP', $member->phone ?: '-'],
            ['Alamat', $member->address ?: '-'],
            ['Bergabung', $member->created_at->translatedFormat('d M Y, H:i') . ' WIB'],
        ];
    @endphp
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col w-full max-w-4xl gap-6 p-6 lg:p-14">
            <div>
                <a href="{{ route('dashboard.members') }}"
                    class="text-white/40 hover:text-white text-sm mb-3 inline-flex items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <div class="flex items-center gap-4">
                    <x-dashboard.avatar :name="$member->name" size="w-14 h-14 text-xl" />
                    <div class="min-w-0">
                        <h1 class="text-2xl font-bold truncate">{{ $member->name }}</h1>
                        <p class="text-white/40 text-sm">Anggota sejak {{ $member->created_at->translatedFormat('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <x-dashboard.stat-card label="Total Pesanan" :value="$orderCount" />
                <x-dashboard.stat-card label="Total Belanja" :value="$rupiah($totalSpent)" value-class="text-green-400"
                    note="Dari pesanan yang sudah selesai" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <x-dashboard.panel title="Data Anggota" class="lg:col-span-3">
                    <dl class="flex flex-col divide-y divide-white/5">
                        @foreach ($profile as [$label, $value])
                            <div class="flex flex-col sm:flex-row sm:gap-4 py-3 first:pt-0 last:pb-0">
                                <dt class="text-white/40 text-[11px] uppercase tracking-wide sm:w-28 shrink-0 sm:pt-0.5">
                                    {{ $label }}</dt>
                                <dd class="text-sm wrap-break-word min-w-0">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </x-dashboard.panel>

                <x-dashboard.panel title="Status Pesanan" class="lg:col-span-2">
                    <div class="flex flex-col gap-2.5">
                        @foreach (\App\Models\Order::STATUS_META as $status => $meta)
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-white/60">
                                    <span class="w-2 h-2 rounded-full"
                                        style="background-color: {{ $meta['color'] }}"></span>
                                    {{ $meta['label'] }}
                                </span>
                                <span class="font-semibold">{{ $statusCounts[$status] }}</span>
                            </div>
                        @endforeach
                    </div>
                </x-dashboard.panel>
            </div>

            <x-dashboard.panel title="Riwayat Pesanan" class="gap-3">
                @forelse ($orders as $order)
                    <a href="{{ route('dashboard.orders.show', $order) }}"
                        class="flex items-center justify-between gap-3 py-2.5 {{ !$loop->last ? 'border-b border-white/6' : '' }} hover:bg-white/5 -mx-2 px-2 rounded-lg transition">
                        <div class="flex flex-col min-w-0">
                            <span class="text-sm font-semibold truncate">{{ $order->order_number }}</span>
                            <span class="text-white/30 text-[11px]">
                                {{ $order->created_at->translatedFormat('d M Y') }} · {{ $order->items->sum('quantity') }}
                                barang
                            </span>
                        </div>
                        <div class="flex items-center gap-2.5 shrink-0">
                            <span class="text-xs font-semibold">{{ $rupiah($order->total) }}</span>
                            <x-dashboard.status-badge :order="$order" class="rounded-md px-2" />
                        </div>
                    </a>
                @empty
                    <p class="text-white/30 text-sm py-4 text-center">Anggota ini belum pernah memesan.</p>
                @endforelse

                @if ($orderCount > $orders->count())
                    <p class="text-white/30 text-xs text-center pt-1">
                        Menampilkan {{ $orders->count() }} pesanan terbaru dari {{ $orderCount }}.
                    </p>
                @endif
            </x-dashboard.panel>
        </div>
    </div>
@endsection
