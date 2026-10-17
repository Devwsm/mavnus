{{--
    Bendera bahasa (SVG inline, tanpa request gambar tambahan & aman untuk CSP img-src 'self').
    Props: $code ('id' | 'en'), $class (ukuran/utility Tailwind, default w-6 h-4)
    ID dibuat unik per pemanggilan karena <clipPath> tidak boleh duplikat dalam satu halaman.
--}}
@php
    $uid = 'flag' . \Illuminate\Support\Str::random(6);
    $class = $class ?? 'w-6 h-4';
@endphp
@if ($code === 'en')
    <svg viewBox="0 0 60 30" preserveAspectRatio="none" aria-hidden="true" focusable="false"
        class="{{ $class }} rounded-[3px] ring-1 ring-black/10 shrink-0">
        <clipPath id="{{ $uid }}s">
            <path d="M0,0 v30 h60 v-30 z" />
        </clipPath>
        <clipPath id="{{ $uid }}t">
            <path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z" />
        </clipPath>
        <g clip-path="url(#{{ $uid }}s)">
            <path d="M0,0 v30 h60 v-30 z" fill="#012169" />
            <path d="M0,0 L60,30 M60,0 L0,30" stroke="#fff" stroke-width="6" />
            <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#{{ $uid }}t)" stroke="#C8102E"
                stroke-width="4" />
            <path d="M30,0 v30 M0,15 h60" stroke="#fff" stroke-width="10" />
            <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6" />
        </g>
    </svg>
@else
    <svg viewBox="0 0 3 2" preserveAspectRatio="none" aria-hidden="true" focusable="false"
        class="{{ $class }} rounded-[3px] ring-1 ring-black/10 shrink-0">
        <rect width="3" height="1" fill="#E70011" />
        <rect y="1" width="3" height="1" fill="#fff" />
    </svg>
@endif
