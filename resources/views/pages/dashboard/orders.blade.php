@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')
        <div class="flex flex-col items-center justify-center p-8 text-center">
            <h1 class="text-3xl font-bold">Pesanan</h1>
            <p class="text-white/50 text-sm mt-2 max-w-md">
                Pantau dan kelola semua pesanan yang masuk dari sini.
            </p>
        </div>

        <div class="flex flex-col w-full max-w-6xl gap-5 p-6 lg:p-14">
            {{-- Ringkasan cepat --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $pendingCount = $orders->where('status', 'pending')->count();
                    $processingCount = $orders->where('status', 'processing')->count();
                    $shippedCount = $orders->where('status', 'shipped')->count();
                    $completedCount = $orders->where('status', 'completed')->count();
                @endphp

                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-1">
                    <span class="text-[#B77B1C] text-2xl font-bold">{{ $pendingCount }}</span>
                    <span class="text-white/40 text-xs uppercase tracking-wide">Pending</span>
                </div>
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-1">
                    <span class="text-[#1C1CB7] text-2xl font-bold">{{ $processingCount }}</span>
                    <span class="text-white/40 text-xs uppercase tracking-wide">Diproses</span>
                </div>
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-1">
                    <span class="text-[#5E1C5E] text-2xl font-bold">{{ $shippedCount }}</span>
                    <span class="text-white/40 text-xs uppercase tracking-wide">Dikirim</span>
                </div>
                <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-1">
                    <span class="text-[#1C7B1C] text-2xl font-bold">{{ $completedCount }}</span>
                    <span class="text-white/40 text-xs uppercase tracking-wide">Selesai</span>
                </div>
            </div>

            {{-- Grid card order --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse ($orders as $order)
                    @php
                        $statusStyle = match ($order->status) {
                            'pending' => ['bg-[#B77B1C]/10 text-[#B77B1C]', 'bi-hourglass-split'],
                            'processing' => ['bg-[#1C1CB7]/10 text-[#1C1CB7]', 'bi-gear-fill'],
                            'shipped' => ['bg-[#5E1C5E]/10 text-[#5E1C5E]', 'bi-truck'],
                            'completed' => ['bg-[#1C7B1C]/10 text-[#1C7B1C]', 'bi-check-circle-fill'],
                            'cancelled' => ['bg-[#B71C1C]/10 text-[#e05656]', 'bi-x-circle-fill'],
                        };
                        $previewImages = $order->items->take(3);
                        $extraItemCount = $order->items->count() - 3;
                    @endphp

                    <div
                        class="bg-[#0D0D0D] border border-white/10 rounded-2xl p-5 flex flex-col gap-4 hover:border-white/20 transition-all duration-300">
                        {{-- Header card --}}
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col">
                                <span class="font-bold text-white tracking-wide">{{ $order->order_number }}</span>
                                <span class="text-white/40 text-xs mt-0.5">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                            <span
                                class="inline-flex items-center gap-1.5 {{ $statusStyle[0] }} text-[10px] font-semibold uppercase tracking-wide px-2.5 py-1 rounded-full">
                                <i class="bi {{ $statusStyle[1] }}"></i>
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        {{-- Preview foto barang --}}
                        <div class="flex items-center gap-2">
                            @foreach ($previewImages as $item)
                                <div
                                    class="w-12 h-12 rounded-lg overflow-hidden bg-black border border-white/10 shrink-0 flex items-center justify-center">
                                    @if ($item->product_image)
                                        <img src="{{ Storage::url($item->product_image) }}"
                                            class="w-full h-full object-cover object-center">
                                    @elseif ($item->product && $item->product->images->first())
                                        <img src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                            class="w-full h-full object-cover object-center">
                                    @else
                                        <i class="bi bi-image text-white/20"></i>
                                    @endif
                                </div>
                            @endforeach

                            @if ($extraItemCount > 0)
                                <div
                                    class="w-12 h-12 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center shrink-0">
                                    <span class="text-white/60 text-xs font-semibold">+{{ $extraItemCount }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Footer card --}}
                        <div class="flex items-center justify-between pt-3 border-t border-white/6">
                            <div class="flex flex-col">
                                <span class="text-white/40 text-[11px] uppercase tracking-wide">Pembeli</span>
                                <span class="text-white text-sm font-medium">{{ $order->customer_name }}</span>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-white/40 text-[11px] uppercase tracking-wide">Total</span>
                                <span class="text-white font-bold">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Payment indicator --}}
                        <div class="flex items-center gap-1.5 text-xs">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-[#1C7B1C]' : 'bg-white/20' }}"></span>
                            <span class="{{ $order->payment_status === 'paid' ? 'text-[#1C7B1C]' : 'text-white/40' }}">
                                {{ $order->payment_status === 'paid' ? 'Sudah dibayar' : 'Menunggu pembayaran' }}
                            </span>
                        </div>

                        {{-- Tombol Detail --}}
                        <a href="{{ route('dashboard.orders.show', $order) }}"
                            class="flex items-center justify-center gap-1.5 bg-[#B71C1C] hover:bg-[#891212] text-white text-sm font-semibold uppercase tracking-wide py-2.5 rounded-lg transition">
                            <i class="bi bi-eye"></i>
                            Lihat Detail
                        </a>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center gap-2 py-20">
                        <i class="bi bi-receipt text-white/15 text-4xl"></i>
                        <p class="text-white/30 text-sm">Belum ada pesanan masuk.</p>
                    </div>
                @endforelse
            </div>

            @if ($orders->hasPages())
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
