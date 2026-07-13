@extends('template.layout')
@section('content')
    <div class="relative flex">
        @include('components/navbar')
    </div>
    <section class="flex flex-col w-full bg-white gap-10 p-6 lg:p-14 pt-28 md:pt-22 lg:pt-32">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold uppercase tracking-wide">Informasi & Bantuan</h1>
            <p class="text-sm text-gray-500 mt-2">Semua yang perlu kamu tahu soal belanja di Mavnus.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            {{-- Sidebar navigasi --}}
            <div class="lg:col-span-1">
                <div
                    class="lg:sticky lg:top-28 flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-2 lg:pb-0 border-b lg:border-b-0 border-black/10">
                    <a href="#store"
                        class="footer-info-link whitespace-nowrap text-sm font-semibold uppercase tracking-wide px-3 py-2 rounded-lg hover:bg-black/5 transition">Search</a>
                    <a href="#returns"
                        class="footer-info-link whitespace-nowrap text-sm font-semibold uppercase tracking-wide px-3 py-2 rounded-lg hover:bg-black/5 transition">Returns
                        & Exchanges</a>
                    <a href="#contact"
                        class="footer-info-link whitespace-nowrap text-sm font-semibold uppercase tracking-wide px-3 py-2 rounded-lg hover:bg-black/5 transition">Contact
                        Support</a>
                    <a href="#terms"
                        class="footer-info-link whitespace-nowrap text-sm font-semibold uppercase tracking-wide px-3 py-2 rounded-lg hover:bg-black/5 transition">Terms
                        & Conditions</a>
                    <a href="#privacy"
                        class="footer-info-link whitespace-nowrap text-sm font-semibold uppercase tracking-wide px-3 py-2 rounded-lg hover:bg-black/5 transition">Privacy
                        Policy</a>
                    <a href="#cookie"
                        class="footer-info-link whitespace-nowrap text-sm font-semibold uppercase tracking-wide px-3 py-2 rounded-lg hover:bg-black/5 transition">Cookie
                        Policy</a>
                </div>
            </div>

            {{-- Konten --}}
            <div class="lg:col-span-3 flex flex-col gap-16">

                {{-- Search --}}
                <div id="store" class="scroll-mt-28 flex flex-col gap-3">
                    <h2 class="text-lg font-bold uppercase tracking-wide border-b border-black/10 pb-3">Search</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Gunakan ikon kaca pembesar di navbar untuk mencari produk berdasarkan nama, baik itu clothes,
                        accessories, maupun albums. Hasil pencarian akan muncul secara langsung saat kamu mengetik.
                    </p>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Belum menemukan yang kamu cari? Coba kata kunci yang lebih umum, atau jelajahi kategori
                        lewat menu navigasi utama.
                    </p>
                </div>

                {{-- Returns & Exchanges --}}
                <div id="returns" class="scroll-mt-28 flex flex-col gap-3">
                    <h2 class="text-lg font-bold uppercase tracking-wide border-b border-black/10 pb-3">Returns & Exchanges
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Kami menerima pengembalian atau penukaran barang dalam waktu 7 hari sejak barang diterima,
                        selama produk belum dipakai, masih dalam kondisi asli, dan menyertakan label/kemasan
                        original.
                    </p>
                    <ul class="list-disc list-inside text-sm text-gray-600 leading-relaxed flex flex-col gap-1">
                        <li>Barang diskon/sale bersifat final sale, tidak dapat dikembalikan.</li>
                        <li>Biaya pengiriman balik ditanggung oleh pembeli, kecuali barang cacat produksi.</li>
                        <li>Proses refund diproses dalam 3–5 hari kerja setelah barang kami terima.</li>
                    </ul>
                </div>

                {{-- Contact Support --}}
                <div id="contact" class="scroll-mt-28 flex flex-col gap-3">
                    <h2 class="text-lg font-bold uppercase tracking-wide border-b border-black/10 pb-3">Contact Support</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Ada pertanyaan soal pesanan, produk, atau kendala lainnya? Tim kami siap membantu.
                    </p>
                    <div class="flex flex-col gap-2 text-sm text-gray-600">
                        <p><span class="font-semibold text-black">Email:</span> support@mavnus.com</p>
                        <p><span class="font-semibold text-black">WhatsApp:</span> +62 812-xxxx-xxxx</p>
                        <p><span class="font-semibold text-black">Jam operasional:</span> Senin–Jumat, 09.00–17.00 WIB</p>
                    </div>
                </div>

                {{-- Terms & Conditions --}}
                <div id="terms" class="scroll-mt-28 flex flex-col gap-3">
                    <h2 class="text-lg font-bold uppercase tracking-wide border-b border-black/10 pb-3">Terms & Conditions
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Dengan melakukan pemesanan di Mavnus, kamu dianggap telah membaca dan menyetujui syarat &
                        ketentuan berikut:
                    </p>
                    <ul class="list-disc list-inside text-sm text-gray-600 leading-relaxed flex flex-col gap-1">
                        <li>Seluruh harga yang tercantum sudah dalam Rupiah dan belum termasuk ongkos kirim.</li>
                        <li>Stok produk dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya.</li>
                        <li>Mavnus berhak membatalkan pesanan apabila terjadi kendala stok atau pembayaran.</li>
                        <li>Foto produk adalah representasi asli, namun warna dapat sedikit berbeda tergantung layar
                            perangkat.</li>
                    </ul>
                </div>

                {{-- Privacy Policy --}}
                <div id="privacy" class="scroll-mt-28 flex flex-col gap-3">
                    <h2 class="text-lg font-bold uppercase tracking-wide border-b border-black/10 pb-3">Privacy Policy</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Kami menghargai privasi kamu. Data pribadi yang kamu berikan (nama, alamat, kontak) hanya
                        digunakan untuk keperluan pemrosesan pesanan dan komunikasi terkait transaksi.
                    </p>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Kami tidak akan membagikan, menjual, atau menyewakan data pribadimu kepada pihak ketiga
                        tanpa izin, kecuali diwajibkan oleh hukum yang berlaku.
                    </p>
                </div>

                {{-- Cookie Policy --}}
                <div id="cookie" class="scroll-mt-28 flex flex-col gap-3">
                    <h2 class="text-lg font-bold uppercase tracking-wide border-b border-black/10 pb-3">Cookie Policy</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Website ini menggunakan cookie untuk meningkatkan pengalaman belanja kamu, seperti
                        mengingat isi keranjang dan preferensi tampilan. Dengan terus menggunakan situs ini, kamu
                        menyetujui penggunaan cookie sesuai kebijakan ini.
                    </p>
                </div>

            </div>
        </div>
    </section>

    @once
        <script>
            // Highlight link sidebar sesuai section yang lagi aktif di layar
            const footerInfoLinks = document.querySelectorAll('.footer-info-link');
            const footerInfoSections = document.querySelectorAll(
                '[id="store"], [id="returns"], [id="contact"], [id="terms"], [id="privacy"], [id="cookie"]');

            function updateActiveFooterInfoLink() {
                let current = '';

                footerInfoSections.forEach(section => {
                    const rect = section.getBoundingClientRect();
                    if (rect.top <= 120) {
                        current = section.id;
                    }
                });

                footerInfoLinks.forEach(link => {
                    const isActive = link.getAttribute('href') === '#' + current;
                    link.classList.toggle('bg-black', isActive);
                    link.classList.toggle('text-white', isActive);
                });
            }

            window.addEventListener('scroll', updateActiveFooterInfoLink);
            updateActiveFooterInfoLink();

            // Smooth scroll saat klik link sidebar
            footerInfoLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();

                    const targetId = link.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);

                    if (targetSection) {
                        const offsetTop = targetSection.getBoundingClientRect().top + window.pageYOffset - 100;

                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth',
                        });
                    }
                });
            });
        </script>
    @endonce
    
@endsection
