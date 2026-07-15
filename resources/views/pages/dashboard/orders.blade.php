@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col items-center bg-black text-white w-full min-h-screen mb-24">
        @include('components/dashboard/navbar')

        <div class="flex flex-col w-full max-w-6xl gap-4 p-6 lg:p-14">
            <h1 class="text-3xl font-bold">Pesanan</h1>
            <div class="bg-[#0D0D0D] border border-white/10 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-white/40 uppercase text-xs text-left">
                            <th class="px-6 py-4">No. Order</th>
                            <th class="px-6 py-4">Pembeli</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-white/5">
                                <td class="px-6 py-4 font-semibold">{{ $order->order_number }}</td>
                                <td class="px-6 py-4">{{ $order->customer_name }}</td>
                                <td class="px-6 py-4">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">{{ ucfirst($order->status) }}</td>
                                <td class="px-6 py-4">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('dashboard.orders.show', $order) }}">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
