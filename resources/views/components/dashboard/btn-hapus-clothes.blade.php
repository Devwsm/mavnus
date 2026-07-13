<form id="deleteForm-{{ $product->id_product }}" action="{{ route('clothes.destroy', $product) }}" method="POST"
    class="hidden">
    @csrf
    @method('DELETE')
</form>

<button type="button" onclick="confirmDelete('{{ $product->id_product }}', '{{ $product->name }}')"
    class="text-[#B71C1C] transition">
    <i class="bi bi-trash"></i>
</button>
@once
    <script>
        function confirmDelete(id, name) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus produk ini?',
                text: `${name} akan dihapus permanen dan tidak bisa dibatalkan.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B71C1C',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm-' + id).submit();
                }
            });
        }
    </script>
@endonce
