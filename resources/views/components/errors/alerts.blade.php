{{-- escapeHtml didefinisikan di template/layout.blade.php, tetapi halaman login/daftar/akun/dashboard
     memakai layout lain. Tanpa fallback ini, popup error di halaman-halaman itu tidak muncul sama sekali
     (ReferenceError di dalam DOMContentLoaded). --}}
<script>
    if (typeof window.escapeHtml !== 'function') {
        window.escapeHtml = function(value) {
            return String(value ?? '').replace(/[&<>"']/g, (ch) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            })[ch]);
        };
    }
</script>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi kesalahan',
                // Pesan di-escape lewat JS (bukan disisipkan mentah ke template literal) supaya
                // karakter ` atau ${ di pesan error tidak bisa merusak/menyuntik script ini
                html: '<ul style="text-align: left; padding-left: 1.2rem;">' +
                    @json($errors->all()).map(msg => '<li>' + escapeHtml(msg) + '</li>').join('') +
                    '</ul>',
                confirmButtonColor: '#B71C1C',
            });
        });
    </script>
@endif

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success')),
                confirmButtonColor: '#1C7B1C ',
                timer: 2500,
                timerProgressBar: true,
            });
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('error')),
                confirmButtonColor: '#B71C1C',
            });
        });
    </script>
@endif
