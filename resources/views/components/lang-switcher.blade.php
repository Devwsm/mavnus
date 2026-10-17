{{--
    Pengganti bahasa (Indonesia / English) pakai bendera.
    Props:
    - $variant : 'dropdown' (navbar desktop, bendera + chevron lalu daftar) | 'list' (drawer mobile) | 'pills' (footer)
    - $uid     : prefix id unik per pemanggilan (hindari ID ganda di halaman)
    Pindah bahasa lewat GET /lang/{locale} (LocaleController), lalu balik ke halaman yang sama.
--}}
@php
    $variant = $variant ?? 'dropdown';
    $uid = $uid ?? 'lang';
    $current = app()->getLocale();
    $codes = ['en', 'id']; // urutan tampil: English dulu, lalu Bahasa Indonesia
@endphp

@if ($variant === 'dropdown')
    <div class="relative" data-lang-switcher>
        <button type="button" id="{{ $uid }}Btn" aria-haspopup="true" aria-expanded="false"
            aria-controls="{{ $uid }}Menu" aria-label="{{ __('site.lang.change') }}"
            class="inline-flex min-h-11 items-center gap-1.5 px-2 text-white/80 hover:text-white transition">
            @include('components.flag', ['code' => $current, 'class' => 'w-6 h-4'])
            <i class="bi bi-chevron-down text-xs" aria-hidden="true"></i>
        </button>
        <ul id="{{ $uid }}Menu"
            class="hidden absolute right-0 top-full mt-2 w-52 bg-white text-black rounded-lg shadow-lg p-1.5 z-50">
            @foreach ($codes as $code)
                <li>
                    <a href="{{ route('lang.switch', $code) }}" lang="{{ $code }}"
                        hreflang="{{ $code }}" @if ($code === $current) aria-current="true" @endif
                        class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm transition {{ $code === $current ? 'bg-gray-100 font-semibold' : 'hover:bg-gray-50' }}">
                        @include('components.flag', ['code' => $code, 'class' => 'w-6 h-4'])
                        {{ __('site.lang.' . $code) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@elseif ($variant === 'list')
    <ul class="flex flex-col gap-1 w-full">
        @foreach ($codes as $code)
            <li>
                <a href="{{ route('lang.switch', $code) }}" lang="{{ $code }}" hreflang="{{ $code }}"
                    @if ($code === $current) aria-current="true" @endif
                    class="flex min-h-11 items-center gap-3 text-base {{ $code === $current ? 'text-white font-semibold' : 'text-white/60' }}">
                    @include('components.flag', ['code' => $code, 'class' => 'w-6 h-4'])
                    {{ __('site.lang.' . $code) }}
                </a>
            </li>
        @endforeach
    </ul>
@else
    <ul class="flex items-center gap-2">
        @foreach ($codes as $code)
            <li>
                <a href="{{ route('lang.switch', $code) }}" lang="{{ $code }}" hreflang="{{ $code }}"
                    @if ($code === $current) aria-current="true" @endif
                    class="inline-flex min-h-11 items-center gap-2 rounded-full border px-3.5 text-xs font-semibold transition {{ $code === $current ? 'border-white bg-white text-black' : 'border-white/30 text-white/70 hover:border-white/60 hover:text-white' }}">
                    @include('components.flag', ['code' => $code, 'class' => 'w-5 h-3.5'])
                    {{ __('site.lang.' . $code) }}
                </a>
            </li>
        @endforeach
    </ul>
@endif

@once
    <script>
        // Dropdown bahasa (navbar desktop): buka/tutup, klik di luar & Escape menutup
        (function() {
            document.querySelectorAll('[data-lang-switcher]').forEach((box) => {
                const btn = box.querySelector('button');
                const menu = box.querySelector('ul');

                function setOpen(open) {
                    menu.classList.toggle('hidden', !open);
                    btn.setAttribute('aria-expanded', String(open));
                }

                btn.addEventListener('click', () => setOpen(menu.classList.contains('hidden')));
                document.addEventListener('click', (e) => {
                    if (!box.contains(e.target)) setOpen(false);
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !menu.classList.contains('hidden')) {
                        setOpen(false);
                        btn.focus();
                    }
                });
            });
        })
        ();
    </script>
@endonce
