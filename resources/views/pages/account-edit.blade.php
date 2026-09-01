{{--
    Halaman "Edit Profil" - terpisah dari halaman overview akun.
    Diakses lewat pensil di pages.account. Gaya "app shell": top bar dengan
    tombol back, background abu-abu muda, card putih shadow lembut, input
    besar & nyaman disentuh, tombol simpan sticky di bawah layar buat mobile.
--}}
@extends('template.account-layout')
@section('hideBottomNav', 'yes')
@section('content')
    <section id="main-content"
        class="flex flex-col w-full bg-[#F5F6F8] gap-4 p-3 sm:p-6 lg:p-14 pt-24 md:pt-22 lg:pt-32 pb-28 lg:pb-14 min-h-screen">
        @include('components/errors/alerts')

        {{-- Top bar: back + judul --}}
        <div
            class="flex items-center gap-3 bg-white rounded-2xl shadow-sm shadow-black/5 px-4 py-3.5 lg:bg-transparent lg:shadow-none lg:px-0 lg:py-0">
            <a href="{{ route('account') }}" aria-label="Kembali ke Akun Saya"
                class="w-9 h-9 rounded-full bg-gray-100 lg:border lg:border-black/10 lg:bg-white flex items-center justify-center shrink-0 hover:bg-gray-200 lg:hover:bg-gray-50 active:scale-95 transition">
                <i class="bi bi-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-base sm:text-xl lg:text-3xl font-bold lg:uppercase lg:tracking-wide">Edit Profil</h1>
                <p class="hidden lg:block text-sm text-gray-500 mt-1">Perbarui data diri kamu.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 lg:col-start-1">
                <form id="editProfileForm" action="{{ route('account.update') }}" method="POST"
                    class="flex flex-col gap-5 bg-white rounded-2xl shadow-sm shadow-black/5 p-4 sm:p-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-semibold mb-1.5">Nama</label>
                        <div class="relative">
                            <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="w-full bg-gray-50 border border-transparent rounded-xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-black/80 focus:border-black transition">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold mb-1.5">Email</label>
                        <div class="relative">
                            <i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                class="w-full bg-gray-50 border border-transparent rounded-xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-black/80 focus:border-black transition">
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold mb-1.5">Nomor HP</label>
                        <div class="relative">
                            <i class="bi bi-telephone absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full bg-gray-50 border border-transparent rounded-xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-black/80 focus:border-black transition"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-semibold mb-1.5">Alamat</label>
                        <div class="relative">
                            <i class="bi bi-geo-alt absolute left-4 top-3.5 text-gray-400"></i>
                            <textarea id="address" name="address" rows="3"
                                class="w-full bg-gray-50 border border-transparent rounded-xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:bg-white focus:ring-2 focus:ring-black/80 focus:border-black transition resize-none"
                                placeholder="Alamat lengkap buat pengiriman">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>

                    @if (!$user->google_id)
                        <input type="hidden" name="current_password" id="currentPasswordField">
                    @endif

                    {{-- Tombol simpan: dalam form buat desktop --}}
                    <button type="button" onclick="confirmSaveProfile()"
                        class="hidden lg:flex items-center justify-center gap-2 bg-black hover:bg-black/85 text-white font-bold text-sm py-3.5 rounded-xl transition mt-2">
                        <i class="bi bi-check2-circle"></i> Simpan Perubahan
                    </button>
                </form>

                {{-- Danger zone: hapus akun --}}
                <div class="bg-white rounded-2xl shadow-sm shadow-black/5 p-4 sm:p-6 mt-4 flex flex-col gap-2">
                    <h2 class="text-sm font-bold text-[#B71C1C]">Hapus Akun</h2>
                    <p class="text-xs text-gray-500">
                        Akun & data profil kamu bakal dihapus permanen dan gak bisa dibalikin. Riwayat pesanan tetap
                        tersimpan buat catatan toko, tapi udah gak nyantol ke akun kamu lagi.
                    </p>
                    <form id="deleteAccountForm" action="{{ route('account.destroy') }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                        @if (!$user->google_id)
                            <input type="hidden" name="password" id="deleteAccountPasswordField">
                        @endif
                    </form>
                    <button type="button" onclick="confirmDeleteAccount()"
                        class="self-start flex items-center gap-2 text-xs font-semibold text-[#B71C1C] hover:underline mt-1">
                        <i class="bi bi-trash3"></i> Hapus akun saya
                    </button>
                </div>
            </div>
        </div>
    </section>

    @once
        <script>
            function confirmSaveProfile() {
                const form = document.getElementById('editProfileForm');

                // Validasi bawaan browser dulu (required, format email, dst)
                // sebelum nembak popup password - biar gak nanya password
                // duluan padahal ada field lain yang belum keisi bener.
                if (!form.reportValidity()) return;

                @if ($user->google_id)
                    form.submit();
                @else
                    Swal.fire({
                        icon: 'question',
                        title: 'Simpan perubahan?',
                        text: 'Masukin password buat konfirmasi.',
                        input: 'password',
                        inputPlaceholder: 'Password saat ini',
                        inputAttributes: {
                            autocapitalize: 'off',
                            autocomplete: 'current-password',
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Ya, simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#000000',
                        cancelButtonColor: '#6b7280',
                        reverseButtons: true,
                        preConfirm: (password) => {
                            if (!password) {
                                Swal.showValidationMessage('Password wajib diisi.');
                            }
                            return password;
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('currentPasswordField').value = result.value;
                            form.submit();
                        }
                    });
                @endif
            }

            function confirmDeleteAccount() {
                @if ($user->google_id)
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hapus akun kamu?',
                        text: 'Semua data profil bakal dihapus permanen dan gak bisa dibalikin.',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus akun',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#B71C1C',
                        cancelButtonColor: '#6b7280',
                        reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('deleteAccountForm').submit();
                        }
                    });
                @else
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hapus akun kamu?',
                        text: 'Semua data profil bakal dihapus permanen dan gak bisa dibalikin.',
                        input: 'password',
                        inputPlaceholder: 'Masukin password buat konfirmasi',
                        inputAttributes: {
                            autocapitalize: 'off',
                            autocomplete: 'current-password',
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus akun',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#B71C1C',
                        cancelButtonColor: '#6b7280',
                        reverseButtons: true,
                        preConfirm: (password) => {
                            if (!password) {
                                Swal.showValidationMessage('Password wajib diisi.');
                            }
                            return password;
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('deleteAccountPasswordField').value = result.value;
                            document.getElementById('deleteAccountForm').submit();
                        }
                    });
                @endif
            }
        </script>
    @endonce

    {{-- Tombol simpan sticky di mobile, gaya app e-commerce --}}
    <div class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-100 p-3 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] z-40"
        style="padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 0.75rem);">
        <button type="button" onclick="confirmSaveProfile()"
            class="flex items-center justify-center gap-2 w-full bg-black hover:bg-black/85 text-white font-bold text-sm py-3.5 rounded-xl transition active:scale-[0.99]">
            <i class="bi bi-check2-circle"></i> Simpan Perubahan
        </button>
    </div>
@endsection
