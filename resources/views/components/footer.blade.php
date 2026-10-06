<footer class="flex flex-col w-full justify-center items-center bg-black">
    <div class="flex flex-col w-fit text-white">
        <!-- ===================== Quick Links ===================== -->
        <div class="border-b border-white/10 px-6 py-6">
            <ul
                class="flex flex-wrap justify-center lg:justify-start gap-x-8 gap-y-3 text-sm uppercase tracking-wide font-semibold">
                <li><a href="{{ route('footer') }}#store" class="hover:opacity-70 transition">Search</a></li>
                <li><a href="{{ route('footer') }}#returns" class="hover:opacity-70 transition">Returns &amp;
                        Exchanges</a>
                </li>
                <li><a href="{{ route('footer') }}#contact" class="hover:opacity-70 transition">Contact Support</a></li>
                <li><a href="{{ route('footer') }}#terms" class="hover:opacity-70 transition">Terms &amp; Conditions</a>
                </li>
                <li><a href="{{ route('footer') }}#privacy" class="hover:opacity-70 transition">Privacy Policy</a></li>
                <li><a href="{{ route('footer') }}#cookie" class="hover:opacity-70 transition">Cookie Policy</a></li>
            </ul>
        </div>

        <!-- ===================== Newsletter + Region ===================== -->
        <div class="px-6 py-10 grid grid-cols-1 md:grid-cols-2 gap-10 border-b border-white/10">
            <!-- Newsletter -->
            <div class="max-w-md mx-auto lg:mx-0 text-center lg:text-left">
                <h2 class="font-bold uppercase tracking-wide mb-4">Subscribe To Our Emails</h2>

                <form id="newsletterForm" action="{{ route('newsletter.subscribe') }}" method="POST" novalidate
                    class="flex items-center border-b border-white/50 pb-2">
                    @csrf
                    <label for="newsletterEmail" class="sr-only">Alamat email</label>
                    <input type="email" id="newsletterEmail" name="email" placeholder="Email" autocomplete="email"
                        required maxlength="255"
                        class="bg-transparent outline-none placeholder-white/60 text-white w-full text-sm">
                    {{-- Honeypot: manusia tidak melihatnya, bot biasanya mengisinya --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true"
                        class="absolute left-[-9999px] h-0 w-0 opacity-0">
                    <button type="submit" aria-label="Berlangganan newsletter"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center text-xl shrink-0">
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>

                <p id="newsletterMessage" role="status" aria-live="polite" class="text-xs mt-2 min-h-4"></p>

                <p class="text-xs text-white/60 mt-4 leading-relaxed">
                    Get updates from <a href="{{ route('home') }}" class="underline hover:text-white">Mavnus</a>
                    and affiliated partners. I understand I can unsubscribe at any time and that my information
                    will be used as described in the site's
                    <a href="{{ route('home') }}#terms" class="underline hover:text-white">Terms &amp; Conditions</a>
                    and <a href="{{ route('home') }}#privacy" class="underline hover:text-white">Privacy Policy</a>.
                </p>
            </div>
            <!-- Region / Currency -->
            <div class="max-w-xs mx-auto lg:mx-0 lg:ml-auto text-center lg:text-left w-full">
                <h2 class="font-bold uppercase tracking-wide mb-4">Country / Region</h2>

                <div class="relative">
                    {{-- Toko ini hanya melayani Indonesia & harga hanya dalam Rupiah. Dulu ada pilihan USD/AUD
                         yang tidak melakukan apa-apa (menyesatkan) dan <select> tanpa label. --}}
                    <p class="w-full bg-white/5 border border-white/30 rounded px-4 py-2 text-sm">Indonesia | IDR Rp</p>
                    <i
                        class="bi bi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-sm pointer-events-none"></i>
                </div>
            </div>
        </div>

        <!-- ===================== Social + Copyright ===================== -->
        <div
            class="px-6 py-6 flex flex-col-reverse md:flex-row items-center justify-between gap-4 text-xs text-white/70">

            <p class="text-center lg:text-left">
                &copy; {{ date('Y') }}, <a href="{{ route('home') }}" class="hover:text-white">Mavnus</a>.
                All rights reserved.
            </p>

            <div class="flex items-center gap-5 text-lg">
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
    </div>
</footer>

<script>
    // Kirim form newsletter lewat fetch supaya halaman tidak reload
    (function() {
        const form = document.getElementById('newsletterForm');
        if (!form) return;
        const message = document.getElementById('newsletterMessage');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            message.className = 'text-xs mt-2 min-h-4 text-white/70';
            message.textContent = 'Mengirim...';

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
                    message.textContent = data.message || 'Terima kasih sudah berlangganan!';
                    form.reset();
                } else {
                    const first = data.errors ? Object.values(data.errors)[0][0] : null;
                    message.className = 'text-xs mt-2 min-h-4 text-red-400';
                    message.textContent = first || (res.status === 429 ?
                        'Terlalu banyak percobaan. Coba lagi sebentar.' :
                        'Gagal berlangganan. Coba lagi.');
                }
            } catch (err) {
                message.className = 'text-xs mt-2 min-h-4 text-red-400';
                message.textContent = 'Gagal berlangganan. Periksa koneksi lalu coba lagi.';
            }
        });
    })();
</script>
