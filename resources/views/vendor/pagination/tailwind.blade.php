{{--
    Override pagination default Laravel (tema dark, sesuai brand Mavnus).
    File ini otomatis kepakai di SEMUA pemanggilan {{ $x->links() }} tanpa
    perlu ubah controller/view manapun — Laravel otomatis pakai file di
    resources/views/vendor/pagination/tailwind.blade.php kalau ada,
    gantiin bawaan package-nya.

    Dipakai di: storefront (clothes/accessories), dashboard
    (orders, visitors, visitor-pages).
--}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
        class="flex flex-wrap items-center justify-center gap-1.5 pt-2">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                class="w-9 h-9 flex items-center justify-center rounded-lg text-white/20 cursor-not-allowed">
                <i class="bi bi-chevron-left text-sm"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                class="w-9 h-9 flex items-center justify-center rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition">
                <i class="bi bi-chevron-left text-sm"></i>
            </a>
        @endif

        {{-- Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span aria-disabled="true"
                    class="w-9 h-9 flex items-center justify-center text-white/30 text-sm select-none">
                    {{ $element }}
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-[#B71C1C] text-white text-sm font-semibold">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-white/60 hover:text-white hover:bg-white/10 text-sm transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                class="w-9 h-9 flex items-center justify-center rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition">
                <i class="bi bi-chevron-right text-sm"></i>
            </a>
        @else
            <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                class="w-9 h-9 flex items-center justify-center rounded-lg text-white/20 cursor-not-allowed">
                <i class="bi bi-chevron-right text-sm"></i>
            </span>
        @endif
    </nav>
@endif
