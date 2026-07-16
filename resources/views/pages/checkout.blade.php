@extends('template.layout')
@section('content')
    <div class="relative flex">
        @include('components/navbar')
    </div>
    <section class="flex flex-col w-full bg-white gap-10 p-6 lg:p-14 pt-28 md:pt-22 lg:pt-32">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">Checkout</h1>
            <p class="text-sm text-gray-500 mt-2">Lengkapi data pengiriman untuk menyelesaikan pesanan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Ringkasan cart --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-28 flex flex-col gap-4 border border-black/10 rounded-xl p-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">Ringkasan Pesanan</h2>

                    <div class="flex flex-col gap-4 divide-y divide-black/5">
                        @foreach ($cartItems as $item)
                            <div class="flex gap-3 pt-4 first:pt-0">
                                <div
                                    class="w-14 h-14 rounded-lg overflow-hidden bg-gray-100 shrink-0 flex items-center justify-center">
                                    @if ($item->product->images->first())
                                        <img src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                            class="w-full h-full object-cover object-center">
                                    @else
                                        <i class="bi bi-image text-gray-300"></i>
                                    @endif
                                </div>
                                <div class="flex flex-col flex-1 gap-0.5">
                                    <span class="text-sm font-semibold">{{ $item->product->name }}</span>
                                    @if ($item->variant)
                                        <span class="text-xs text-gray-500">Size: {{ $item->variant->label }}</span>
                                    @endif
                                    <div class="flex items-center justify-between text-sm mt-1">
                                        <span class="text-gray-500">{{ $item->quantity }}x</span>
                                        <span
                                            class="font-semibold">Rp{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col gap-2 pt-4 border-t border-black/10">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Total Berat</span>
                            <span class="text-gray-600">{{ $cartItems->sum(fn($i) => $i->product->weight * $i->quantity) }}
                                gram</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span
                                class="font-semibold">Rp{{ number_format($cartItems->sum(fn($i) => $i->product->price * $i->quantity), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Ongkir</span>
                            <span class="text-gray-400">Dihitung nanti</span>
                        </div>
                        <div class="flex items-center justify-between text-base font-bold pt-2 border-t border-black/10">
                            <span>Total</span>
                            <span>Rp{{ number_format($cartItems->sum(fn($i) => $i->product->price * $i->quantity), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form data customer --}}
            <form action="{{ route('order.store') }}" method="POST" class="lg:col-span-2 flex flex-col gap-6">
                @csrf
                <div class="flex flex-col gap-4 border border-black/10 rounded-xl p-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">Data Penerima</h2>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Nama Lengkap</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="Nama penerima">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Nomor HP</label>
                        <input type="number" name="customer_phone" value="{{ old('customer_phone') }}"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Alamat Lengkap</label>
                        <textarea name="customer_address" rows="3"
                            class="w-full border border-black/10 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-black"
                            placeholder="Nama jalan, nomor rumah, kelurahan, kecamatan, kota, kode pos">{{ old('customer_address') }}</textarea>
                    </div>
                </div>

                {{-- Placeholder ongkir — nanti diisi RajaOngkir --}}
                <div class="flex flex-col gap-2 border border-black/10 rounded-xl p-6 bg-gray-50">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">Pengiriman</h2>
                    <p class="text-sm text-gray-500">Estimasi ongkir akan dihitung otomatis berdasarkan alamat kamu.</p>
                </div>
                {{-- Placeholder payment — nanti diisi Midtrans --}}
                <div class="flex flex-col gap-2 border border-black/10 rounded-xl p-6 bg-gray-50">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black/50">Metode Pembayaran</h2>
                    <p class="text-sm text-gray-500">Pilihan pembayaran akan tersedia setelah fitur ini aktif.</p>
                </div>
                <button type="submit"
                    class="bg-black hover:bg-black/80 text-white uppercase font-bold tracking-widest py-3.5 rounded-lg transition">
                    Buat Pesanan
                </button>
            </form>

        </div>
    </section>

    @include('components/footer')
@endsection
