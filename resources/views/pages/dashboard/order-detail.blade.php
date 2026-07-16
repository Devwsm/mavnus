@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col w-full max-w-4xl gap-6 p-6 lg:p-14">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('dashboard.orders') }}"
                        class="text-white/40 hover:text-white text-sm mb-2 inline-flex items-center gap-1">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <h1 class="text-2xl font-bold uppercase tracking-wide">{{ $order->order_number }}</h1>
                    <p class="text-white/40 text-sm mt-1">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                @php
                    $statusColor = match ($order->status) {
                        'pending' => 'bg-amber-500/10 text-amber-400',
                        'processing' => 'bg-blue-500/10 text-blue-400',
                        'shipped' => 'bg-purple-500/10 text-purple-400',
                        'completed' => 'bg-green-500/10 text-green-400',
                        'cancelled' => 'bg-[#B71C1C]/10 text-[#e05656]',
                    };
                @endphp
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $statusColor }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Kiri: rincian item + data pembeli --}}
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <div class="bg-[#0D0D0D] border border-white/10 rounded-xl p-6 flex flex-col gap-4">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Rincian Pesanan</h2>
                        <div class="flex flex-col divide-y divide-white/5">
                            @foreach ($order->items as $item)
                                <div class="flex items-center gap-3 py-3 first:pt-0">
                                    <div
                                        class="w-14 h-14 rounded-lg overflow-hidden bg-black shrink-0 flex items-center justify-center">
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
                                    <div class="flex flex-col flex-1">
                                        <span class="text-sm font-semibold">{{ $item->product_name }}</span>
                                        @if ($item->variant_label)
                                            <span class="text-xs text-white/40">Size: {{ $item->variant_label }}</span>
                                        @endif
                                        <span class="text-xs text-white/40">{{ $item->quantity }}x @
                                            Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                    </div>
                                    <span
                                        class="text-sm font-semibold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-[#0D0D0D] border border-white/10 rounded-xl p-6 flex flex-col gap-2">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C] mb-2">Data Penerima</h2>
                        <p class="text-sm"><span class="text-white/40">Nama:</span> {{ $order->customer_name }}</p>
                        <p class="text-sm"><span class="text-white/40">No. HP:</span> {{ $order->customer_phone }}</p>
                        <p class="text-sm"><span class="text-white/40">Alamat:</span> {{ $order->customer_address }}</p>
                    </div>
                </div>

                {{-- Kanan: ringkasan biaya + update status --}}
                <div class="lg:col-span-1 flex flex-col gap-6">
                    <div class="bg-[#0D0D0D] border border-white/10 rounded-xl p-6 flex flex-col gap-2">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C] mb-2">Ringkasan</h2>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-white/40">Total Berat</span>
                            <span>{{ $order->total_weight }} gram</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-white/40">Subtotal</span>
                            <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-white/40">Ongkir</span>
                            <span
                                class="text-white/40">{{ $order->shipping_cost > 0 ? 'Rp' . number_format($order->shipping_cost, 0, ',', '.') : 'Belum dihitung' }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between text-base font-bold pt-2 mt-1 border-t border-white/10">
                            <span>Total</span>
                            <span>Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm pt-2 mt-1 border-t border-white/10">
                            <span class="text-white/40">Pembayaran</span>
                            <span class="{{ $order->payment_status === 'paid' ? 'text-green-400' : 'text-amber-400' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-[#0D0D0D] border border-white/10 rounded-xl p-6 flex flex-col gap-3">
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-[#B71C1C]">Update Status</h2>
                        <form action="{{ route('dashboard.orders.updateStatus', $order) }}" method="POST"
                            class="flex flex-col gap-3">
                            @csrf
                            @method('PATCH')
                            <select name="status"
                                class="w-full bg-black border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm focus:outline-none focus:border-[#B71C1C]">
                                @foreach (['pending', 'processing', 'shipped', 'completed', 'cancelled'] as $statusOption)
                                    <option value="{{ $statusOption }}" @selected($order->status === $statusOption)>
                                        {{ ucfirst($statusOption) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="bg-[#B71C1C] hover:bg-[#891212] text-white text-sm font-semibold uppercase tracking-wide py-2.5 rounded-lg transition">
                                Simpan Status
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
