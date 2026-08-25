{{-- Halaman "Riwayat Pesanan" customer, sidebar sama persis kayak account.blade.php --}}
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
                            class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold uppercase tracking-wide hover:bg-gray-50 transition">
                            <i class="bi bi-person-fill"></i> Akun Saya
                        </a>
                        <a href="{{ route('account.orders') }}"
                            class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold uppercase tracking-wide border-t border-black/10 bg-black text-white">
                            <i class="bi bi-bag-fill"></i> Riwayat Pesanan
                        </a>
                        <a href="{{ route('logout') }}"
                            class="flex items-center gap-3 px-5 py-3.5 text-sm font-semibold uppercase tracking-wide border-t border-black/10 hover:bg-gray-50 transition text-gray-500">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Konten: list order --}}
            <div class="lg:col-span-2 flex flex-col gap-4">
                @forelse ($orders as $order)
                    <a href="{{ route('order.success', $order) }}"
                        class="flex flex-col gap-4 border border-black/10 rounded-xl p-6 hover:border-black/30 transition">
                        <div class="flex items-center justify-between pb-4 border-b border-black/10">
                            <div>
                                <p class="text-sm font-semibold">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>
                            </div>
                            @php
                                $statusStyle = match ($order->status) {
                                    'completed' => ['bg-green-50', 'text-green-600', 'bg-green-500'],
                                    'cancelled' => ['bg-red-50', 'text-red-600', 'bg-red-500'],
                                    'shipped', 'processing' => ['bg-blue-50', 'text-blue-600', 'bg-blue-500'],
                                    default => ['bg-amber-50', 'text-amber-600', 'bg-amber-500'],
                                };
                            @endphp
                            <span
                                class="inline-flex items-center gap-1.5 {{ $statusStyle[0] }} {{ $statusStyle[1] }} text-xs font-semibold uppercase tracking-wide px-3 py-1.5 rounded-full shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusStyle[2] }}"></span>
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-2">
                            @foreach ($order->items->take(2) as $item)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ $item->quantity }}x {{ $item->product_name }}</span>
                                    <span class="font-semibold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                            @if ($order->items->count() > 2)
                                <p class="text-xs text-gray-400">+{{ $order->items->count() - 2 }} produk lainnya</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-black/10">
                            <span class="text-sm text-gray-500">Total Pesanan</span>
                            <span class="text-base font-bold">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center text-center gap-3 border border-black/10 rounded-xl p-10">
                        <i class="bi bi-bag text-3xl text-gray-300"></i>
                        <p class="text-sm text-gray-500">Kamu belum punya pesanan apa pun.</p>
                        <a href="{{ route('home') }}"
                            class="text-sm font-semibold underline underline-offset-4 decoration-black/30 hover:decoration-black transition">
                            Mulai belanja
                        </a>
                    </div>
                @endforelse

                @if ($orders->hasPages())
                    <div class="mt-2">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
    @include('components/footer')
@endsection
