<footer class="flex flex-col w-full justify-center items-center bg-[#5E0006]">
    <div class="flex flex-col w-fit text-white">
        <!-- ===================== Quick Links ===================== -->
        <div class="border-b border-white/10 px-6 py-6">
            <ul
                class="flex flex-wrap justify-center lg:justify-start gap-x-8 gap-y-3 text-sm uppercase tracking-wide font-semibold">
                <li><a href="{{ route('home') }}#store" class="hover:opacity-70 transition">Search</a></li>
                <li><a href="{{ route('home') }}#returns" class="hover:opacity-70 transition">Returns &amp; Exchanges</a>
                </li>
                <li><a href="{{ route('home') }}#contact" class="hover:opacity-70 transition">Contact Support</a></li>
                <li><a href="{{ route('home') }}#terms" class="hover:opacity-70 transition">Terms &amp; Conditions</a>
                </li>
                <li><a href="{{ route('home') }}#privacy" class="hover:opacity-70 transition">Privacy Policy</a></li>
                <li><a href="{{ route('home') }}#cookie" class="hover:opacity-70 transition">Cookie Policy</a></li>
            </ul>
        </div>

        <!-- ===================== Newsletter + Region ===================== -->
        <div class="px-6 py-10 grid grid-cols-1 md:grid-cols-2 gap-10 border-b border-white/10">
            <!-- Newsletter -->
            <div class="max-w-md mx-auto lg:mx-0 text-center lg:text-left">
                <h2 class="font-bold uppercase tracking-wide mb-4">Subscribe To Our Emails</h2>

                <form class="flex items-center border-b border-white/50 pb-2">
                    <input type="email" placeholder="Email"
                        class="bg-transparent outline-none placeholder-white/60 text-white w-full text-sm">
                    <button type="submit" class="text-xl shrink-0">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

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
                    <select
                        class="w-full appearance-none bg-white/5 border border-white/30 rounded px-4 py-2 text-sm outline-none">
                        <option class="bg-[#5E0006] text-white">Indonesia | IDR Rp</option>
                        <option class="bg-[#5E0006] text-white">United States | USD $</option>
                        <option class="bg-[#5E0006] text-white">Australia | AUD $</option>
                    </select>
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
                <a href="#" class="hover:opacity-70 transition"><i class="bi bi-instagram"></i></a>
                <a href="#" class="hover:opacity-70 transition"><i class="bi bi-facebook"></i></a>
                <a href="#" class="hover:opacity-70 transition"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="hover:opacity-70 transition"><i class="bi bi-youtube"></i></a>
                <a href="#" class="hover:opacity-70 transition"><i class="bi bi-spotify"></i></a>
            </div>
        </div>
    </div>
</footer>
