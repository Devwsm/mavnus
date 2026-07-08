<section class="flex flex-col w-full max-w-7xl gap-24 p-6 lg:p-14 pt-22 md:pt-18 lg:pt-28">

    <!-- ===================== Albums ===================== -->
    <div class="flex flex-col gap-4">
        @include('components/filters')

        <!-- ===================== Product Albums Grid ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            @forelse ($products as $product)
                <a href="{{ route('product_detail.albums', $product) }}"
                    class="group flex flex-col bg-black gap-4 p-5 rounded-2xl overflow-hidden border border-white/5 hover:border-white/15 hover:bg-black/80 transition-all duration-300">
                    <div class="w-full aspect-square overflow-hidden rounded-lg">
                        @if ($product->images->first())
                            <img src="{{ Storage::url($product->images->first()->image_path) }}" loading="lazy"
                                decoding="async" alt="{{ $product->name }}"
                                class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-white/5">
                                <i class="bi bi-image text-white/20 text-3xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col text-center gap-1">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-white transition">
                            {{ $product->name }}
                        </h3>
                        <p class="text-sm text-white/70">{{ $product->formatted_price }}</p>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-center text-black/40 text-sm py-10">
                    Belum ada produk albums tersedia.
                </p>
            @endforelse

        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</section>
