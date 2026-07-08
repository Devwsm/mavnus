<form method="GET" action="{{ route(request()->route()->getName()) }}" class="flex flex-col gap-4">
    <div class="flex items-center gap-3">
        <div class="flex flex-col gap-1 flex-1">
            <label class="text-xs font-semibold uppercase tracking-wide text-black/50">Min</label>
            <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0"
                class="w-full border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-black">
        </div>
        <div class="flex flex-col gap-1 flex-1">
            <label class="text-xs font-semibold uppercase tracking-wide text-black/50">Max</label>
            <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="1000000"
                class="w-full border border-black/10 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-black">
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button type="submit"
            class="flex-1 bg-black hover:bg-black/80 text-white text-sm font-semibold uppercase tracking-wide px-4 py-2 rounded-lg transition">
            Terapkan
        </button>
        @if (request('price_min') || request('price_max'))
            <a href="{{ route(request()->route()->getName()) }}"
                class="text-sm text-black/50 hover:text-black underline underline-offset-4 px-2 py-2 whitespace-nowrap">
                Reset
            </a>
        @endif
    </div>
</form>
