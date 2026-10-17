@php
    // Tautan footer. Label diambil dari file terjemahan (lang/*/site.php) supaya ikut bahasa yang dipilih.
    $footerShop = [
        ['label' => __('site.nav.home'), 'href' => route('home')],
        ['label' => __('site.nav.clothes'), 'href' => route('clothes')],
        ['label' => __('site.nav.accessories'), 'href' => route('accessoris')],
        ['label' => __('site.nav.my_account'), 'href' => route('account')],
    ];
    $footerHelp = [
        ['label' => __('site.footer.link_search'), 'href' => route('footer') . '#store'],
        ['label' => __('site.footer.link_returns'), 'href' => route('footer') . '#returns'],
        ['label' => __('site.footer.link_contact'), 'href' => route('footer') . '#contact'],
    ];
    $footerPolicy = [
        ['label' => __('site.footer.link_terms'), 'href' => route('footer') . '#terms'],
        ['label' => __('site.footer.link_privacy'), 'href' => route('footer') . '#privacy'],
        ['label' => __('site.footer.link_cookie'), 'href' => route('footer') . '#cookie'],
    ];
    $footerGroups = [
        ['id' => 'footerShopTitle', 'title' => __('site.footer.shop_title'), 'links' => $footerShop],
        ['id' => 'footerHelpTitle', 'title' => __('site.footer.help_title'), 'links' => $footerHelp],
        ['id' => 'footerPolicyTitle', 'title' => __('site.footer.policy_title'), 'links' => $footerPolicy],
    ];
    $footerLinkClass = 'inline-flex min-h-11 items-center text-sm text-white/70 hover:text-white transition';
@endphp
<footer class="w-full bg-black text-white">
    <div class="mx-auto w-full max-w-7xl px-6 lg:px-14">

        <!-- ===================== Brand | Menu | Newsletter ===================== -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-x-10 gap-y-12 py-12 border-b border-white/10">

            <!-- Brand + sosial media -->
            <div
                class="flex flex-col gap-5 items-center text-center md:items-start md:text-left md:order-1 lg:col-span-3">
                <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center">
                    <img src="{{ \App\Support\Img::url('aset/logo/Whisnu-Santika_Logo-2025-White.webp', 'aset/logo/Whisnu-Santika_Logo-2025-White.png') }}"
                        width="624" height="89" loading="lazy" decoding="async" alt="Whisnu Santika"
                        class="w-44 h-auto">
                </a>
                <p class="text-sm text-white/70 leading-relaxed max-w-xs">{{ __('site.footer.about') }}</p>

                <div class="flex items-center gap-1 text-lg">
                    <a href="https://www.instagram.com/whisnusantika/" target="_blank" rel="noopener noreferrer"
                        aria-label="Instagram Whisnu Santika"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center hover:opacity-70 transition"><i
                            class="bi bi-instagram" aria-hidden="true"></i></a>
                    <a href="https://www.youtube.com/@WhisnuSantika" target="_blank" rel="noopener noreferrer"
                        aria-label="YouTube Whisnu Santika"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center hover:opacity-70 transition"><i
                            class="bi bi-youtube" aria-hidden="true"></i></a>
                    <a href="https://open.spotify.com/artist/6gvsmDZKW5wRvjKCPnbHDh?si=7jt9_kpmTsCcL-pVJYnblQ"
                        target="_blank" rel="noopener noreferrer" aria-label="Spotify Whisnu Santika"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center hover:opacity-70 transition"><i
                            class="bi bi-spotify" aria-hidden="true"></i></a>
                </div>
            </div>

            <!-- Menu: Belanja | Bantuan | Kebijakan -->
            <nav aria-label="{{ __('site.footer.nav_label') }}"
                class="grid grid-cols-2 sm:grid-cols-3 gap-x-8 gap-y-10 md:col-span-2 md:order-3 lg:col-span-5 lg:order-2">
                @foreach ($footerGroups as $group)
                    <div>
                        <h2 id="{{ $group['id'] }}"
                            class="text-xs font-bold uppercase tracking-widest text-white/60 mb-3">
                            {{ $group['title'] }}</h2>
                        <ul aria-labelledby="{{ $group['id'] }}" class="flex flex-col">
                            @foreach ($group['links'] as $link)
                                <li><a href="{{ $link['href'] }}"
                                        class="{{ $footerLinkClass }}">{{ $link['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            <!-- Newsletter -->
            <div class="md:order-2 lg:col-span-4 lg:order-3 text-center md:text-left">
                <h2 class="text-xs font-bold uppercase tracking-widest text-white/60 mb-4">
                    {{ __('site.footer.newsletter_title') }}</h2>

                <form id="newsletterForm" action="{{ route('newsletter.subscribe') }}" method="POST" novalidate
                    class="flex items-center border-b border-white/50 pb-2">
                    @csrf
                    <label for="newsletterEmail" class="sr-only">{{ __('site.footer.email_label') }}</label>
                    <input type="email" id="newsletterEmail" name="email"
                        placeholder="{{ __('site.footer.email_placeholder') }}" autocomplete="email" required
                        maxlength="255"
                        class="bg-transparent outline-none placeholder-white/60 text-white w-full text-sm">
                    {{-- Honeypot: manusia tidak melihatnya, bot biasanya mengisinya --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true"
                        class="absolute left-[-9999px] h-0 w-0 opacity-0">
                    <button type="submit" aria-label="{{ __('site.footer.subscribe_aria') }}"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center text-xl shrink-0">
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <p id="newsletterMessage" role="status" aria-live="polite" class="text-xs mt-2 min-h-4"></p>

                {{-- Tautan syarat & privasi diarahkan ke halaman /info (dulu salah ke beranda) --}}
                <p class="text-xs text-white/60 mt-4 leading-relaxed">
                    {!! __('site.footer.newsletter_note', [
                        'brand' => '<a href="' . e(route('home')) . '" class="underline hover:text-white">Mavnus</a>',
                        'terms' =>
                            '<a href="' .
                            e(route('footer')) .
                            '#terms" class="underline hover:text-white">' .
                            e(__('site.footer.link_terms')) .
                            '</a>',
                        'privacy' =>
                            '<a href="' .
                            e(route('footer')) .
                            '#privacy" class="underline hover:text-white">' .
                            e(__('site.footer.link_privacy')) .
                            '</a>',
                    ]) !!}
                </p>
            </div>
        </div>

        <!-- ===================== Bahasa + Wilayah + Copyright ===================== -->
        <div class="py-6 flex flex-col-reverse md:flex-row items-center justify-between gap-4 text-xs text-white/70">
            <p class="text-center md:text-left">
                {!! __('site.footer.copyright', [
                    'year' => date('Y'),
                    'brand' => '<a href="' . e(route('home')) . '" class="hover:text-white">Mavnus</a>',
                ]) !!}
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-6">
                {{-- Toko ini hanya melayani Indonesia & harga hanya dalam Rupiah (tidak ada pilihan negara/mata uang) --}}
                <p>
                    <span class="text-white/60">{{ __('site.footer.region_title') }}:</span>
                    <span class="font-semibold text-white">{{ __('site.footer.region_value') }}</span>
                </p>
                <div class="flex items-center gap-3">
                    <span class="text-white/60">{{ __('site.lang.label') }}:</span>
                    @include('components/lang-switcher', ['variant' => 'pills', 'uid' => 'langFooter'])
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    // Teks untuk JavaScript (ikut bahasa yang dipilih)
    const footerI18n = @js([
    'sending' => __('site.footer.sending'),
    'subscribed' => __('site.footer.subscribed'),
    'tooMany' => __('site.footer.too_many'),
    'failed' => __('site.footer.failed'),
    'networkFailed' => __('site.footer.network_failed'),
]);

    // Kirim form newsletter lewat fetch supaya halaman tidak reload
    (function() {
        const form = document.getElementById('newsletterForm');
        if (!form) return;
        const message = document.getElementById('newsletterMessage');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            message.className = 'text-xs mt-2 min-h-4 text-white/70';
            message.textContent = footerI18n.sending;

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content,
                    },
                    body: new FormData(form),
                });
                const data = await res.json().catch(() => ({}));

                if (res.ok) {
                    message.className = 'text-xs mt-2 min-h-4 text-green-400';
                    message.textContent = data.message || footerI18n.subscribed;
                    form.reset();
                } else {
                    const first = data.errors ? Object.values(data.errors)[0][0] : null;
                    message.className = 'text-xs mt-2 min-h-4 text-red-400';
                    message.textContent = first || (res.status === 429 ?
                        footerI18n.tooMany :
                        footerI18n.failed);
                }
            } catch (err) {
                message.className = 'text-xs mt-2 min-h-4 text-red-400';
                message.textContent = footerI18n.networkFailed;
            }
        });
    })();
</script>
